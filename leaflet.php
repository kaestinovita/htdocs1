<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web GIS</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>
        body {
            background-color: #fcebf8;
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            color: #6c112d;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            margin-top: 20px;
        }

        table {
            width: 40%;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #6c112d;
            color: #ffffff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1def5;
            color: #fff;
        }

        #map {
            width: 50%;
            height: 400px;
            border: 1px solid #ddd;
        }

        .delete-btn {
            color: #ffffff;
            background-color: #ff4d4d;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .delete-btn:hover {
            background-color: #ff1a1a;
        }
    </style>
</head>

<body>
    <h2>Web GIS<br>Kabupaten Sleman</h2>

    <div class="container">
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
                echo "<p style='text-align: center; color: red;'>Error menghapus data: " . htmlspecialchars($conn->error) . "</p>";
            }

            $stmt->close();
        }

        // Query untuk mengambil data dari tabel
        $sql = "SELECT kecamatan, Luas, Longitude, Latitude, Jumlah_Penduduk FROM penduduk";
        $result = $conn->query($sql);

        // Cek apakah query berhasil
        if ($result === false) {
            die("Query gagal: " . htmlspecialchars($conn->error));
        }

        if ($result->num_rows > 0) {
            echo "<table><tr> 
                    <th>Kecamatan</th> 
                    <th>Longitude</th>
                    <th>Latitude</th>
                    <th>Luas</th>
                    <th>Jumlah Penduduk</th>
                    <th>Aksi</th>
                  </tr>";

            // Inisialisasi array untuk menyimpan koordinat
            echo "<script>var locations = [];</script>";

            // Menampilkan data tiap baris
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["kecamatan"]) . "</td>
                        <td>" . htmlspecialchars($row["Longitude"]) . "</td>
                        <td>" . htmlspecialchars($row["Latitude"]) . "</td>
                        <td>" . htmlspecialchars($row["Luas"]) . "</td>
                        <td align='right'>" . htmlspecialchars($row["Jumlah_Penduduk"]) . "</td>
                        <td>
                            <form method='POST' action=''>
                                <input type='hidden' name='delete_kecamatan' value='" . htmlspecialchars($row["kecamatan"]) . "'>
                                <input type='submit' class='delete-btn' value='Hapus'>
                            </form>
                        </td>
                      </tr>";

                // Menambahkan koordinat ke dalam array JavaScript
                echo "<script>locations.push({
                    kecamatan: '" . addslashes($row["kecamatan"]) . "',
                    longitude: " . addslashes($row["Longitude"]) . ",
                    latitude: " . addslashes($row["Latitude"]) . ",
                    jumlahPenduduk: " . addslashes($row["Jumlah_Penduduk"]) . "
                });</script>";
            }
            echo "</table>";
        } else {
            echo "<p style='text-align: center; color: red;'>0 hasil</p>";
        }

        // Menutup koneksi
        $conn->close();
        ?>

        <!-- Div untuk peta -->
        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-7.797068, 110.370529], 10); // Atur ke posisi pusat dan zoom

        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Tambahkan marker untuk setiap lokasi dalam array
        locations.forEach(function(location) {
            var marker = L.marker([location.latitude, location.longitude]).addTo(map);
            marker.bindPopup("<b>" + location.kecamatan + "</b><br>" +
                "Jumlah Penduduk: " + location.jumlahPenduduk);
        });
    </script>
</body>

</html>
