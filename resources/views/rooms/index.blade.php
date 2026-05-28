<!DOCTYPE html>
<html>

<head>
    <title>Data Room</title>
</head>

<body>

    <h1>Data Room</h1>

    <a href="/rooms/create">
        Tambah Room
    </a>

    <br><br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Nama Ruangan</th>
            <th>Kapasitas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        @foreach($rooms as $room)

            <tr>
                <td>{{ $room->room_id }}</td>
                <td>{{ $room->nama_ruangan }}</td>
                <td>{{ $room->kapasitas }}</td>
                <td>{{ $room->status }}</td>

                <td>

                    <a href="/rooms/edit/{{ $room->room_id }}">
                        Edit
                    </a>

                    |

                    <a href="/rooms/delete/{{ $room->room_id }}"
                        onclick="return confirm('Yakin ingin menghapus room ini?')">

                        Delete

                    </a>

                </td>
            </tr>

        @endforeach

    </table>

</body>

</html>