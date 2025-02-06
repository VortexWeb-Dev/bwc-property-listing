<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing</title>
    <link rel="shortcut icon" href="assets/images/company-logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="./node_modules/apexcharts/dist/apexcharts.css">
    <!-- jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- swapy cdn -->
    <!-- <script src="https://unpkg.com/swapy/dist/swapy.min.js"></script> -->
    <script src="./node_modules/swapy/dist/swapy.min.js"></script>
    <link rel="stylesheet" href="assets/css/shuffle.css">
    <style>
        #property-table {
            height: 75vh;
            /* Or any fixed height you prefer */
            max-height: 800px;
            /* Optional: maximum height */
            position: relative;
            margin: 20px;
        }

        /* Table wrapper styles */
        .table-container {
            height: 100%;
            overflow: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }

        /* Make the header stick to the top */
        thead tr th {
            position: sticky;
            top: 0;
            background-color: white;
            z-index: 1;
        }

        /* Ensure the table takes full width of its container */
        .table-responsive {
            min-width: 100%;
            width: max-content;
            /* This allows the table to expand beyond viewport width */
        }
    </style>
</head>

<body class="">