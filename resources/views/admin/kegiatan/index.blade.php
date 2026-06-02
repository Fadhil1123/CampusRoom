<!DOCTYPE html>
<html>

<head>
    <title>Data Kegiatan</title>
</head>

<body>

    <h1>Data Kegiatan</h1>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Nama Kegiatan</th>
            <th>Deskripsi</th>
            <th>Penyelenggara</th>
            <th>Aksi</th>
        </tr>

        @foreach($kegiatan as $item)

        <tr>

            <td>{{ $item->kegiatan_id }}</td>

            <td>{{ $item->nama_kegiatan }}</td>

            <td>{{ $item->deskripsi }}</td>

            <td>{{ $item->penyelenggara }}</td>

            <td>

                <a href="/admin/kegiatan/edit/{{ $item->kegiatan_id }}">
                    Edit
                </a>

                |

                <a href="/admin/kegiatan/delete/{{ $item->kegiatan_id }}"
                onclick="return confirm('Yakin ingin menghapus kegiatan ini?')">
                    Hapus
                </a>

            </td>

        </tr>

        @endforeach

    </table>

</body>

</html>