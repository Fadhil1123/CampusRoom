<!DOCTYPE html>
<html>

<head>
    <title>Edit Kegiatan</title>
</head>

<body>

<h1>Edit Kegiatan</h1>

<form action="/admin/kegiatan/update/{{ $kegiatan->kegiatan_id }}"
    method="POST">

    @csrf
    @method('PUT')

    <div>

        <label>Nama Kegiatan</label>

        <br>

        <input type="text"
            name="nama_kegiatan"
            value="{{ $kegiatan->nama_kegiatan }}">

    </div>

    <br>

    <div>

        <label>Deskripsi</label>

        <br>

        <textarea name="deskripsi">{{ $kegiatan->deskripsi }}</textarea>

    </div>

    <br>

    <div>

        <label>Penyelenggara</label>

        <br>

        <input type="text"
            name="penyelenggara"
            value="{{ $kegiatan->penyelenggara }}">

    </div>

    <br>

    <button type="submit">

        Update

    </button>

</form>

</body>
</html>