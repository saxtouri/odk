<?php require_once('config.php');

class ODKDB {
    private $conn = NULL;
    public $debug = DEBUG;

    private function log($s) {echo "<p>" . $s . "</p>";}
    private function q($sql) {
        if ($this->debug) $this->log($sql);
        $r = $this->conn->query($sql);
        if ($this->debug && $this->conn->error) $this->log($this->conn->error);
        return $r;
    }

    public function init() {
        $this->q($this->init_applicant);
        $this->q($this->init_institution);
        $this->q($this->init_job);
        $this->q($this->init_application);
    }

    public function close() {
        return $this->conn->close();
    }

    public function start_transaction() {
        return $this->q("START TRANSACTION");
    }

    public function end_transaction($success=TRUE) {
        return $this->q(($success) ? "COMMIT" : "ROLLBACK");
    }


    public function reset() {
        $this->q("DROP TABLES application, job, institution, applicant;");
        $this->init();
    }

    public function __construct() {
        $this->conn = new mysqli(
            DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ($this->conn->connect_error) {
            $this->log($this->conn->connect_error);
            return(0);
        }
        $this->conn->set_charset('utf8');
        $this->init();
    }

    // Applicant
    private $init_applicant = "CREATE TABLE IF NOT EXISTS applicant ("
    . "applicant_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
    . "name VARCHAR(256) NOT NULL UNIQUE, "
    . "points INT NOT NULL"
    . ");";

    public function insert_applicant($name=NULL, $points=0) {
        $this->q("INSERT INTO applicant VALUES ("
        . "NULL, "
        . "\"" . (($name == NULL) ? "" : urlencode($name)). "\", "
        . $points
        . ");");
        return $this->conn->insert_id;
    }

    public function delete_applicant($applicant_id) {
        return($this->q("DELETE FROM applicant "
        . "WHERE applicant_id=\"" . $applicant_id . "\";"
        ));
    }

    function delete_applicant_with_applications($applicant_id) {
        $this->start_transaction();
        $r = $this->q(
            "DELETE FROM application WHERE applicant_id=" . $applicant_id);
        if ($r) $r = $this->q("DELETE FROM applicant "
            . "WHERE applicant_id=" . $applicant_id
            );
        return $this->end_transaction($r);
    }

    // Institution
    private $init_institution = "CREATE TABLE IF NOT EXISTS institution ("
    . "institution_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
    . "name VARCHAR(256) NOT NULL UNIQUE, "
    . "positions INT DEFAULT 0"
    . ");";


    public function insert_institution($name, $positions=0) {
        $this->q("INSERT INTO institution VALUES ("
        . "NULL, "
        . "\"" . urlencode($name) . "\", "
        . $positions
        . ");");
        return $this->conn->insert_id;
    }

    public function delete_institution($institution_id) {
        return($this->q("DELETE FROM institution "
        . "WHERE institution_id=\"" . $institution_id . "\";"
        ));
    }

    function delete_institution_with_applications($institution_id) {
        $this->start_transaction();
        $this->q(
            "DELETE FROM application WHERE institution_id=" . $institution_id);
        $r = $this->q("DELETE FROM institution "
        . "WHERE institution_id=\"" . $institution_id . "\";"
        );
        return $this->end_transaction($r);
    }

    // job
    private $init_job = "CREATE TABLE IF NOT EXISTS job ("
    . "institution_id INT NOT NULL, "
    . "applicant_id INT NOT NULL, "
    . "FOREIGN KEY (institution_id) REFERENCES institution(institution_id), "
    . "FOREIGN KEY (applicant_id) REFERENCES applicant(applicant_id), "
    . "UNIQUE(institution_id, applicant_id)"
    . ");";

    public function insert_job($institution_id, $applicant_id) {
        $this->q("INSERT INTO job VALUES ("
        . $institution_id . ", ". $applicant_id
        . ");");
        return $this->conn->insert_id;
    }

    // SELECT methods
    public function get_institution_positions() {
        $r = $this->q(
            "SELECT institution_id, name, positions FROM institution");
        $institutions = array();
        while ($r and $row = $r->fetch_assoc()) {
            $row["name"] = urldecode($row["name"]);
            $institutions[$row["institution_id"]] = $row;
        }
        return $institutions;
    }

    public function next_institution() {
        $this->start_transaction();
        $r = $this->q('SELECT * FROM institution ORDER BY institution_id;');
        while ($r and $row = $r->fetch_assoc()) {
            $row['name'] = urldecode($row['name']);
            yield($row);
        }
        $this->end_transaction($r);
    }

    public function next_applicant() {
        $this->start_transaction();
        $r = $this->q('SELECT * FROM applicant ORDER BY applicant_id;');
        while ($r and $row = $r->fetch_assoc()) {
            $row['name'] = urldecode($row['name']);
            yield($row);
        }
        $this->end_transaction($r);
    }

    public function next_job() {
        $this->start_transaction();
        $r = $this->q("SELECT "
        . "A.applicant_id as applicant_id, "
        . "A.name as applicant_name, "
        . "I.institution_id as institution_id, "
        . "I.name as institution_name, "
        . "A.points as points, P.preference as preference "
        . "FROM applicant A, institution I, job J, application P "
        . "WHERE "
        . "A.applicant_id=J.applicant_id AND "
        . "I.institution_id=J.institution_id AND "
        . "P.applicant_id=J.applicant_id AND P.institution_id=J.institution_id;"
        );
        while ($r and $row = $r->fetch_assoc()) {
            $row['applicant_name'] = urldecode($row['applicant_name']);
            $row['institution_name'] = urldecode($row['institution_name']);
            yield($row);
        }
        $this->end_transaction($r);
    }

    public function next_unpositioned() {
        $this->start_transaction();
        $r = $this->q("SELECT * FROM applicant WHERE applicant_id NOT IN ("
        ."SELECT applicant_id FROM job);");
        while ($r and $row = $r->fetch_assoc()) {
            $row['name'] = urldecode($row['name']);
            yield($row);
        }
        $this->end_transaction($r);
    }

    // Application
    private $init_application = "CREATE TABLE IF NOT EXISTS application ("
    . "applicant_id INT NOT NULL, "
    . "institution_id INT NOT NULL, "
    . "preference INT NOT NULL, "
    . "FOREIGN KEY (applicant_id) REFERENCES applicant(applicant_id), "
    . "FOREIGN KEY (institution_id) REFERENCES institution(institution_id), "
    . "UNIQUE(applicant_id, institution_id), "
    . "UNIQUE(applicant_id, preference)"
    . ");";

    public function insert_application(
            $applicant_id, $institution_id, $preference) {
        $this->q("INSERT INTO application VALUES ("
        . $applicant_id . ", ". $institution_id . ", ". $preference
        . ");");
        return $this->conn->insert_id;
    }

    public function delete_applications_by_applicant($applicant_id) {
        return $this->q(
            "DELETE FROM application WHERE applicant_id=" . $applicant_id);
    }

    public function delete_applications_by_institution($institution_id) {
        return $this->q(
            "DELETE FROM application WHERE institution_id=" . $institution_id);
    }

    public function get_institutions_by_preference($applicant_id) {
        $r = $this->q("SELECT I.* "
        . "FROM application A, institution I "
        . "WHERE A.applicant_id=" . $applicant_id
        . " AND A.institution_id=I.institution_id ORDER BY A.preference;");
        $institutions = array();
        while ($r and $row = $r->fetch_assoc()) {
            $row["name"] = urldecode($row["name"]);
            $institutions[$row["institution_id"]] = $row;
        }
        return $institutions;
    }

    /** Get them in order of right to choose and preference */
    function get_applicants_institutions() {
        $this->start_transaction();
        $r = $this->q("SELECT "
        . "P.applicant_id, P.name, P.points, A.institution_id, A.preference "
        . "FROM applicant P, application A "
        . "WHERE P.applicant_id=A.applicant_id "
        . "ORDER BY P.points DESC, A.preference ASC;"
        );
        while ($r and $row = $r->fetch_assoc()) {
            $row["name"] = urldecode($row["name"]);
            yield($row);
        }
        $this->end_transaction($r);
    }

    // Update methods
    public function update_institution($institution_id, $new_name, $new_positions) {
        $this->q("UPDATE institution SET "
        . "name=\"" . urlencode($new_name) . "\", positions=" . $new_positions
        . " WHERE institution_id=" . $institution_id);
    }

    public function update_applicant($applicant_id, $name, $points) {
        return $this->q("UPDATE applicant SET "
        . "name=\"" . urlencode($name) . "\", points=" . $points
        . " WHERE applicant_id=" . $applicant_id);
    }

    public function clean_applications($applicant_id) {
        return $this->q("DELETE FROM application WHERE applicant_id=" . $applicant_id);
    }

    public function reset_jobs() {
        return $this->q("DELETE FROM job;");
    }

    // Helper methods
    public function print_init_queries() {
        echo "<p>" . $this->init_applicant . "</p>";
        echo "<p>" . $this->init_institution . "</p>";
        echo "<p>" . $this->init_job . "</p>";
        echo "<p>" . $this->init_application . "</p>";
    }
}
?>