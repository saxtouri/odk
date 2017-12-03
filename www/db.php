<?php require_once('config.php');

class ODKDB {
    private $conn = NULL;
    public $debug = DEBUG;

    private function log($s) {echo "<p>" . $s . "</p>";}
    private function q($sql) {
        if ($this->debug) $this->log($sql);
        $r = $this->conn->query($sql);
        if ($this->debug && $this->conn->error) $this->log($this->conn->error);

    }

    public function init() {
        $this->q($this->init_applicant);
        $this->q($this->init_institution);
        $this->q($this->init_job);
        $this->q($this->init_application);
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
    . "name VARCHAR(256), "
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


    // Institution
    private $init_institution = "CREATE TABLE IF NOT EXISTS institution ("
    . "institution_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
    . "name VARCHAR(256)"
    . ");";


    public function insert_institution($name=NULL) {
        $this->q("INSERT INTO institution VALUES ("
        . "NULL, "
        . "\"" . (($name == NULL) ? "" : urlencode($name)). "\""
        . ");");
        return $this->conn->insert_id;
    }

    // job
    private $init_job = "CREATE TABLE IF NOT EXISTS job ("
    . "job_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
    . "institution_id INT NOT NULL, "
    . "applicant_id INT, "
    . "FOREIGN KEY (institution_id) REFERENCES institution(institution_id), "
    . "FOREIGN KEY (applicant_id) REFERENCES applicant(applicant_id)"
    . ");";

    public function insert_job($institution_id, $applicant_id=NULL) {
        $this->q("INSERT INTO job VALUES ("
        . "NULL, "
        . $institution_id . ", "
        . (($applicant_id == NULL) ? "NULL" : $applicant_id)
        . ");");
        return $this->conn->insert_id;
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


    // Helper methods
    public function print_init_queries() {
        echo "<p>" . $this->init_applicant . "</p>";
        echo "<p>" . $this->init_institution . "</p>";
        echo "<p>" . $this->init_job . "</p>";
        echo "<p>" . $this->init_application . "</p>";
    }
}
?>