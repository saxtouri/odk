<?php require_once('config.php');

class ODKDB {
    private $conn = NULL;

    private function q($sql) {return $this->conn->query($sql);}

    public function init_db() {
        $this->q($init_applicant);
    }

    public function reset_db() {
        $this->q("DROP TABLES application, job, institution, applicant;");
        $this->init_db();
    }

    public function __contstruct() {
        $this->conn = new mysqli(
            DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        $this->conn->set_charset('utf8');
        $this->init_db();
    }

    // Applicant
    private $init_applicant = "CREATE TABLE IF NOT EXISTS applicant ("
    . "applicant_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
    . "name VARCHAR(256)"
    . ");";


    // Institution
    private $init_institution = "CREATE TABLE IF NOT EXISTS institution ("
    . "institution_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
    . "name VARCHAR(256)"
    . ");";

    // job
    private $init_job = "CREATE TABLE IF NOT EXISTS job ("
    . "job_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, "
    . "institution_id INT NOT NULL, "
    . "applicant_id INT, "
    . "FOREIGN KEY (institution_id) REFERENCES institution(institution_id), "
    . "FOREIGN KEY (applicant_id) REFERENCES applicant(applicant_id), "
    . "UNIQUE(institution_id, applicant_id)"
    . ");";

    // Application
    private $init_application = "CREATE TABLE IF NOT EXISTS application ("
    . "applicant_id INT NOT NULL, "
    . "job_id INT NOT NULL, "
    . "preference INT NOT NULL, "
    . "FOREIGN KEY (applicant_id) REFERENCES applicant(applicant_id), "
    . "FOREIGN KEY (job_id) REFERENCES job(job_id), "
    . "UNIQUE(applicant_id, job_id), "
    . "UNIQUE(applicant_id, preference)"
    . ");";


    // Helper methods
    public function print_init_queries() {
        echo "<p>" . $this->init_applicant . "</p>";
        echo "<p>" . $this->init_institution . "</p>";
        echo "<p>" . $this->init_job . "</p>";
        echo "<p>" . $this->init_application . "</p>";
    }
}
?>