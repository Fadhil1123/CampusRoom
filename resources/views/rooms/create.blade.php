<!DOCTYPE html>
<html>
<head>
    <title>Tambah Room</title>
</head>
<body>

    <h1>Tambah Room</h1>

    <form action="/rooms/store" method="POST">

        @csrf

        <div>
            <label>Nama Ruangan</label>
            <br>

            <input type="text"
                name="nama_ruangan">
        </div>

        <br>

        <div>
            <label>Kapasitas</label>
            <br>

            <input type="number"
                name="kapasitas">
        </div>

        <br>

        <div>
            <label>Status</label>
            <br>

            <select name="status">

                <option value="tersedia">
                    Tersedia
                </option>

                <option value="tidak tersedia">
                    Tidak Tersedia
                </option>

            </select>
        </div>

        <br>

        <button type="submit">
            Simpan
        </button>

    </form>

    <br>

    <a href="/rooms">
        Kembali
    </a>

</body>
</html>