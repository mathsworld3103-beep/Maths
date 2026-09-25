<?php

require_once "auth-check.php";

header("Content-Type: application/json");

$action = $_GET["action"] ?? $_POST["action"] ?? "";

try {

    /* =========================================
       LIST PAST PAPERS
    ========================================= */

    if ($action === "list") {

        $search = trim($_GET["search"] ?? "");
        $subject = trim($_GET["subject"] ?? "");
        $grade = trim($_GET["grade"] ?? "");

        $sql = "
            SELECT
                id,
                title,
                description,
                subject,
                grade,
                paper_type,
                paper_year,
                file_name,
                file_path,
                created_at
            FROM past_papers
            WHERE 1=1
        ";

        $types = "";
        $params = [];

        if ($search !== "") {

            $sql .= "
                AND (
                    title LIKE ?
                    OR description LIKE ?
                    OR subject LIKE ?
                )
            ";

            $searchValue = "%" . $search . "%";

            $types .= "sss";

            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
        }

        if ($subject !== "") {

            $sql .= " AND subject = ?";

            $types .= "s";
            $params[] = $subject;
        }

        if ($grade !== "") {

            $sql .= " AND grade = ?";

            $types .= "s";
            $params[] = $grade;
        }

        $sql .= "
            ORDER BY paper_year DESC, created_at DESC
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception($conn->error);
        }

        if ($types !== "") {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $papers = [];

        while ($row = $result->fetch_assoc()) {
            $papers[] = $row;
        }

        echo json_encode([
            "success" => true,
            "papers" => $papers,
            "count" => count($papers)
        ]);

        exit;
    }


    /* =========================================
       ADD PAST PAPER
    ========================================= */

    if ($action === "add") {

        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $subject = trim($_POST["subject"] ?? "");
        $grade = trim($_POST["grade"] ?? "");
        $paperType = trim(
            $_POST["paper_type"] ?? "Past Paper"
        );
        $paperYear = (int) (
            $_POST["paper_year"] ?? 0
        );


        /* REQUIRED FIELDS */

        if (
            $title === "" ||
            $subject === "" ||
            $grade === "" ||
            $paperYear < 2000
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Please fill all required fields."
            ]);

            exit;
        }


        /* CHECK FILE */

        if (
            !isset($_FILES["paper"]) ||
            $_FILES["paper"]["error"] !== UPLOAD_ERR_OK
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Please select a PDF file."
            ]);

            exit;
        }


        $file = $_FILES["paper"];


        /* MAX 10 MB */

        if ($file["size"] > 10 * 1024 * 1024) {

            echo json_encode([
                "success" => false,
                "message" => "PDF file must be less than 10 MB."
            ]);

            exit;
        }


        /* EXTENSION */

        $extension = strtolower(
            pathinfo(
                $file["name"],
                PATHINFO_EXTENSION
            )
        );


        if ($extension !== "pdf") {

            echo json_encode([
                "success" => false,
                "message" => "Only PDF files are allowed."
            ]);

            exit;
        }


        /* MIME TYPE */

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $mimeType = finfo_file(
            $finfo,
            $file["tmp_name"]
        );

        finfo_close($finfo);


        if ($mimeType !== "application/pdf") {

            echo json_encode([
                "success" => false,
                "message" => "Invalid PDF file."
            ]);

            exit;
        }


        /* UPLOAD DIRECTORY */

        $uploadDir =
            "../uploads/past-papers/";


        if (!is_dir($uploadDir)) {

            if (!mkdir(
                $uploadDir,
                0777,
                true
            )) {

                echo json_encode([
                    "success" => false,
                    "message" => "Unable to create upload folder."
                ]);

                exit;
            }
        }


        /* UNIQUE FILE NAME */

        $newFileName =
            "paper_" .
            time() .
            "_" .
            bin2hex(random_bytes(5)) .
            ".pdf";


        $destination =
            $uploadDir . $newFileName;


        /* MOVE FILE */

        if (
            !move_uploaded_file(
                $file["tmp_name"],
                $destination
            )
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Unable to save uploaded PDF."
            ]);

            exit;
        }


        /* DATABASE PATH */

        $filePath =
            "uploads/past-papers/" .
            $newFileName;


        $uploadedBy =
            (int) $_SESSION["user_id"];


        /* INSERT */

        $stmt = $conn->prepare("
            INSERT INTO past_papers
            (
                title,
                description,
                subject,
                grade,
                paper_type,
                paper_year,
                file_name,
                file_path,
                uploaded_by
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");


        if (!$stmt) {

            if (file_exists($destination)) {
                unlink($destination);
            }

            throw new Exception(
                "Database prepare error: " .
                $conn->error
            );
        }


        /*
         * 9 values:
         *
         * title       = s
         * description = s
         * subject     = s
         * grade       = s
         * paper_type  = s
         * paper_year  = i
         * file_name   = s
         * file_path   = s
         * uploaded_by = i
         */

        $stmt->bind_param(
            "sssssissi",
            $title,
            $description,
            $subject,
            $grade,
            $paperType,
            $paperYear,
            $file["name"],
            $filePath,
            $uploadedBy
        );


        /*
         * IMPORTANT:
         * The above type string must NOT contain a space.
         */

        $stmt->bind_param(
            "ssssisssi",
            $title,
            $description,
            $subject,
            $grade,
            $paperType,
            $paperYear,
            $file["name"],
            $filePath,
            $uploadedBy
        );


        if (!$stmt->execute()) {

            if (file_exists($destination)) {
                unlink($destination);
            }

            throw new Exception(
                "Database insert error: " .
                $stmt->error
            );
        }


        echo json_encode([
            "success" => true,
            "message" => "Past paper uploaded successfully."
        ]);

        exit;
    }


    /* =========================================
       DELETE
    ========================================= */

    if ($action === "delete") {

        $id = (int) (
            $_POST["id"] ?? 0
        );


        if ($id <= 0) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid paper ID."
            ]);

            exit;
        }


        $stmt = $conn->prepare("
            SELECT file_path
            FROM past_papers
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result =
            $stmt->get_result();


        if ($result->num_rows !== 1) {

            echo json_encode([
                "success" => false,
                "message" => "Past paper not found."
            ]);

            exit;
        }


        $paper =
            $result->fetch_assoc();


        $stmt = $conn->prepare("
            DELETE FROM past_papers
            WHERE id = ?
        ");

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();


        $physicalPath =
            "../" . $paper["file_path"];


        if (
            file_exists($physicalPath)
        ) {

            unlink($physicalPath);
        }


        echo json_encode([
            "success" => true,
            "message" => "Past paper deleted successfully."
        ]);

        exit;
    }


    echo json_encode([
        "success" => false,
        "message" => "Invalid action."
    ]);


} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

?>