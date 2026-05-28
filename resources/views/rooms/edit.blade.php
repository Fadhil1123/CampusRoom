<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
</head>
<body>

    <h1>Edit Room</h1>

    <form action="/rooms/update/{{ $room->room_id }}"
        method="POST">

        @csrf
        @method('PUT')

        <div>
            <label>Nama Ruangan</label>
            <br>

            <input type="text"
                name="nama_ruangan"
                value="{{ $room->nama_ruangan }}">
        </div>

        <br>

        <div>
            <label>Kapasitas</label>
            <br>

            <input type="number"
                name="kapasitas"
                value="{{ $room->kapasitas }}">
        </div>

        <br>

        <div>
            <label>Status</label>
            <br>

            <select name="status">

                <option value="tersedia"
                    {{ $room->status == 'tersedia' ? 'selected' : '' }}>
                    Tersedia
                </option>

                <option value="tidak tersedia"
                    {{ $room->status == 'tidak tersedia' ? 'selected' : '' }}>
                    Tidak Tersedia
                </option>

            </select>
        </div>

        <br>

        <button type="submit">
            Update
        </button>

    </form>

    <br>

    <a href="/rooms">
        Kembali
    </a>

</body>
</html>