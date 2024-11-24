<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEB GIS YOGYAKARTA</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body {
            background-color: #ffe3fa;
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            color: #610c29;
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
            background-color: #8998fe;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #FFE5F1;
            color: #fff;
        }
        #map {
            width: 80%;
            height: 400px;
            margin: 20px auto;
            border: 1px solid #ddd;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
        }
        .close-btn {
            background-color: #ff4d4d;
            color: #ffffff;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h2>WEB GIS YOGYAKARTA</h2>
    
    <!-- Div untuk peta -->
    <div id="map"></div>

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

    // Handle delete request
    if (isset($_POST['delete_kecamatan'])) {
        $delete_kecamatan = $_POST['delete_kecamatan'];
        $delete_sql = "DELETE FROM penduduk WHERE kecamatan = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("s", $delete_kecamatan);
        $stmt->execute();
        $stmt->close();
    }

    // Handle edit/update request
    if (isset($_POST['update'])) {
        $kecamatan = $_POST['kecamatan'];
        $longitude = $_POST['longitude'];
        $latitude = $_POST['latitude'];
        $luas = $_POST['luas'];
        $jumlahPenduduk = $_POST['jumlah_penduduk'];

        $update_sql = "UPDATE penduduk SET Longitude = ?, Latitude = ?, Luas = ?, Jumlah_Penduduk = ? WHERE kecamatan = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("dddis", $longitude, $latitude, $luas, $jumlahPenduduk, $kecamatan);
        $stmt->execute();
        $stmt->close();
    }

    // Query untuk mengambil data dari tabel
    $sql = "SELECT kecamatan, Luas, Longitude, Latitude, Jumlah_Penduduk FROM penduduk"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { 
        echo "<table><tr> 
                <th>Kecamatan</th> 
                <th>Luas</th> 
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Jumlah Penduduk</th>
                <th>Aksi</th>
              </tr>"; 

        echo "<script>var locations = [];</script>";
        
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
                        <button onclick=\"openModal('".$row["kecamatan"]."', '".$row["Longitude"]."', '".$row["Latitude"]."', '".$row["Luas"]."', '".$row["Jumlah_Penduduk"]."')\">Edit</button>
                    </td>
                  </tr>"; 
            
            echo "<script>locations.push({
                    kecamatan: '".addslashes($row["kecamatan"])."',
                    longitude: ".$row["Longitude"].",
                    latitude: ".$row["Latitude"].",
                    jumlahPenduduk: ".$row["Jumlah_Penduduk"]."
                });</script>";
        } 
        echo "</table>"; 
    } else { 
        echo "<p style='text-align: center; color: red;'>0 hasil</p>"; 
    } 

    $conn->close(); 
    ?>

    <!-- Modal for editing data -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <h3>Edit Data Kecamatan</h3>
            <form method="POST" action="">
                <input type="hidden" name="kecamatan" id="editKecamatan">
                <label>Longitude:</label>
                <input type="text" name="longitude" id="editLongitude">
                <label>Latitude:</label>
                <input type="text" name="latitude" id="editLatitude">
                <label>Luas:</label>
                <input type="text" name="luas" id="editLuas">
                <label>Jumlah Penduduk:</label>
                <input type="text" name="jumlah_penduduk" id="editJumlahPenduduk">
                <button type="submit" name="update" class="save-btn">Simpan</button>
                <button type="button" class="close-btn" onclick="closeModal()">Tutup</button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([-7.797068, 110.370529], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        locations.forEach(function(location) {
            var marker = L.marker([location.latitude, location.longitude]).addTo(map);
            marker.bindPopup("<b>" + location.kecamatan + "</b><br>" +
                             "Jumlah Penduduk: " + location.jumlahPenduduk);
        });

        // Open and close modal functions
        function openModal(kecamatan, longitude, latitude, luas, jumlahPenduduk) {
            document.getElementById("editKecamatan").value = kecamatan;
            document.getElementById("editLongitude").value = longitude;
            document.getElementById("editLatitude").value = latitude;
            document.getElementById("editLuas").value = luas;
            document.getElementById("editJumlahPenduduk").value = jumlahPenduduk;
            document.getElementById("editModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }
    </script>
</body>
</html>
