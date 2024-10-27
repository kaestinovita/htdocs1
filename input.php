<!-- input.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Penduduk</title>

    <style>
        body {
            background-color: #e9c7e4;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #a9bee2;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #f1c9cf;
        }
        p {
            text-align: center;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Tambah Data Penduduk</h2>
    <form method="POST" action="input.php">
        <label for="kecamatan">Kecamatan:</label>
        <input type="text" name="kecamatan" required><br>

        <label for="longitude">Longitude:</label>
        <input type="number" step="0.000001" name="Longitude" required><br>

        <label for="latitude">Latitude:</label>
        <input type="number" step="0.000001" name="Latitude" required><br>

        <label for="luas">Luas:</label>
        <input type="number" step="0.01" name="Luas" required><br>

        <label for="jumlah_penduduk">Jumlah Penduduk:</label>
        <input type="number" name="Jumlah_Penduduk" required><br>

        <input type="submit" name="submit" value="Tambah Data">
    </form>
</body>
</html>

<?php
// Periksa apakah tombol submit diklik
if (isset($_POST['submit'])) {
    // Konfigurasi MySQL
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "latihan";

    // Membuat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Ambil data dari form
    $kecamatan = $_POST['kecamatan'];
    $longitude = $_POST['Longitude'];
    $latitude = $_POST['Latitude'];
    $luas = $_POST['Luas'];
    $jumlah_penduduk = $_POST['Jumlah_Penduduk'];

    // Gunakan prepared statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("INSERT INTO penduduk (kecamatan, Longitude, Latitude, Luas, Jumlah_Penduduk) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdddd", $kecamatan, $longitude, $latitude, $luas, $jumlah_penduduk);

    if ($stmt->execute()) {
        echo "<p>Data baru berhasil ditambahkan</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    // Menutup statement dan koneksi
    $stmt->close();
    $conn->close();
}
?>
