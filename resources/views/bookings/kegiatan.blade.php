<!DOCTYPE html>
<html>

<head>
    <title>Booking Kegiatan</title>
</head>

<body>

    <h1>Booking Kegiatan</h1>

    @if($errors->any())

        <ul>

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    @endif

    <form action="/booking/kegiatan/store" method="POST" enctype="multipart/form-data">

        @csrf

        <div>

            <label>Nama Kegiatan</label>

            <br>

            <input type="text" name="nama_kegiatan">

        </div>

        <div>

            <label>Deskripsi Kegiatan</label>

            <br>

            <textarea name="deskripsi"></textarea>

        </div>

        <br>

        <div>

            <label>Penyelenggara</label>

            <br>

            <input type="text" name="penyelenggara">

        </div>

        <br>

        <br>

        <div>

            <label>Tanggal</label>

            <br>

            <input type="date" name="tanggal">

        </div>

        <br>

        <div>

            <label>Jam Mulai</label>

            <br>

            <input type="time" name="jam_mulai">

        </div>

        <br>

        <div>

            <label>Jam Selesai</label>

            <br>

            <input type="time" name="jam_selesai">

        </div>

        <br>

        <div>

            <label>Upload Surat</label>

            <br>

            <input type="file" name="surat">

        </div>

        <br>

        <div>

            <label>Pilih Ruangan</label>

            <br><br>

            @foreach($rooms as $room)

                <input type="checkbox" name="room_id[]" value="{{ $room->room_id }}">

                {{ $room->nama_ruangan }}

                <br>

            @endforeach

        </div>

        <br>

        <button type="submit">
            Booking Kegiatan
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