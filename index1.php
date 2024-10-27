<!-- index.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk</title>
    <style>
        body {
            background-color: #fcebf8;
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            color: #6c112d;
        }
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #c8f3fb;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1def5;
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Data Penduduk</h2>

    <?php
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

    // Memeriksa jika ada permintaan hapus data berdasarkan kecamatan
    if (isset($_POST['delete_kecamatan'])) {
        $delete_kecamatan = $_POST['delete_kecamatan'];
        
        // Query untuk menghapus data berdasarkan kecamatan
        $delete_sql = "DELETE FROM penduduk WHERE kecamatan = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("s", $delete_kecamatan);
        
        if ($stmt->execute()) {
            echo "<p style='text-align: center; color: green;'>Data berhasil dihapus.</p>";
        } else {
            echo "<p style='text-align: center; color: red;'>Error menghapus data: " . $conn->error . "</p>";
        }
        
        $stmt->close();
    }

    // Query untuk mengambil data dari tabel
    $sql = "SELECT kecamatan, Luas, Longitude, Latitude, Jumlah_Penduduk FROM penduduk"; 
    $result = $conn->query($sql);

    // Cek apakah query berhasil
    if ($result === false) {
        die("Query gagal: " . $conn->error);
    }

    if ($result->num_rows > 0) { 
        echo "<table><tr> 
                <th>Kecamatan</th> 
                <th>Luas</th> 
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Jumlah Penduduk</th>
                <th>Aksi</th>
              </tr>"; 

        // Menampilkan data tiap baris
        while($row = $result->fetch_assoc()) { 
            echo "<tr>
                    <td>".$row["kecamatan"]."</td>
                    <td>".$row["Luas"]."</td>
                    <td>".$row["Longitude"]."</td>
                    <td>".$row["Latitude"]."</td>
                    <td align='right'>".$row["Jumlah_Penduduk"]."</td>
                    <td>
                        <form method='POST' action=''>
                            <input type='hidden' name='delete_kecamatan' value='".$row["kecamatan"]."'>
                            <input type='submit' value='Hapus'>
                        </form>
                    </td>
                  </tr>"; 
        } 
        echo "</table>"; 
    } else { 
        echo "<p style='text-align: center; color: red;'>0 hasil</p>"; 
    } 

    // Menutup koneksi
    $conn->close(); 
    ?>

</body>
</html>
