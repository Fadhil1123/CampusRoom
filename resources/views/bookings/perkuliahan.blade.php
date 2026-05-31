<!DOCTYPE html>
<html>
<head>
    <title>Booking Perkuliahan</title>
</head>
<body>

    <h1>Booking Perkuliahan</h1>
    

    @if($errors->any())

        <ul>

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    @endif
    <form action="/booking/perkuliahan/store"
        method="POST">

        @csrf

        <div>

            <label>Tanggal</label>

            <br>

            <input type="date"
                name="tanggal">

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

        <div>

            <label>Ruangan</label>

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

        <button type="submit">
            Booking
        </button>

    </form>

    @if(session('success'))

    <p style="color:green">

        {{ session('success') }}

    </p>

    @endif

    @if(session('error'))

        <p style="color:red">

            {{ session('error') }}

        </p>

    @endif

</body>
</html>