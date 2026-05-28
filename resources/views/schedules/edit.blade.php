<!DOCTYPE html>
<html>
<head>
    <title>Edit Schedule</title>
</head>
<body>

    <h1>Edit Schedule</h1>

    <form action="/schedules/update/{{ $schedule->schedule_id }}"
        method="POST">

        @csrf
        @method('PUT')

        <div>

            <label>Room</label>

            <br>

            <select name="room_id">

                @foreach($rooms as $room)

                    <option value="{{ $room->room_id }}"
                        {{ $schedule->room_id == $room->room_id ? 'selected' : '' }}>

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
                name="mata_kuliah"
                value="{{ $schedule->mata_kuliah }}">

        </div>

        <br>

        <div>

            <label>Dosen</label>

            <br>

            <input type="text"
                name="dosen"
                value="{{ $schedule->dosen }}">

        </div>

        <br>

        <div>

            <label>Hari</label>

            <br>

            <select name="hari">

                <option value="Senin"
                    {{ $schedule->hari == 'Senin' ? 'selected' : '' }}>
                    Senin
                </option>

                <option value="Selasa"
                    {{ $schedule->hari == 'Selasa' ? 'selected' : '' }}>
                    Selasa
                </option>

                <option value="Rabu"
                    {{ $schedule->hari == 'Rabu' ? 'selected' : '' }}>
                    Rabu
                </option>

                <option value="Kamis"
                    {{ $schedule->hari == 'Kamis' ? 'selected' : '' }}>
                    Kamis
                </option>

                <option value="Jumat"
                    {{ $schedule->hari == 'Jumat' ? 'selected' : '' }}>
                    Jumat
                </option>

                <option value="Sabtu"
                    {{ $schedule->hari == 'Sabtu' ? 'selected' : '' }}>
                    Sabtu
                </option>

            </select>

        </div>

        <br>

        <div>

            <label>Jam Mulai</label>

            <br>

            <input type="time"
                name="jam_mulai"
                value="{{ $schedule->jam_mulai }}">

        </div>

        <br>

        <div>

            <label>Jam Selesai</label>

            <br>

            <input type="time"
                name="jam_selesai"
                value="{{ $schedule->jam_selesai }}">

        </div>

        <br>

        <button type="submit">
            Update
        </button>

    </form>

    <br>

    <a href="/schedules">
        Kembali
    </a>

</body>
</html>