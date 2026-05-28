<!DOCTYPE html>
<html>
<head>
    <title>Tambah Schedule</title>
</head>
<body>

    <h1>Tambah Schedule</h1>

    <form action="/schedules/store"
        method="POST">

        @csrf

        <div>

            <label>Room</label>

            <br>

            <select name="room_id">

                @foreach($rooms as $room)

                    <option value="{{ $room->room_id }}">

                        {{ $room->nama_ruangan }}

                    </option>

                @endforeach

            </select>

        </div>

        <br>

        <div>

            <label>Mata Kuliah</label>

            <br>

            <input type="text"
                name="mata_kuliah">

        </div>

        <br>

        <div>

            <label>Dosen</label>

            <br>

            <input type="text"
                name="dosen">

        </div>

        <br>

        <div>

            <label>Hari</label>

            <br>

            <select name="hari">

                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>

            </select>

        </div>

        <br>

        <div>

            <label>Jam Mulai</label>

            <br>

            <input type="time"
                name="jam_mulai">

        </div>

        <br>

        <div>

            <label>Jam Selesai</label>

            <br>

            <input type="time"
                name="jam_selesai">

        </div>

        <br>

        <button type="submit">
            Simpan
        </button>

    </form>

    <br>

    <a href="/schedules">
        Kembali
    </a>

</body>
</html>