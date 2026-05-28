<!DOCTYPE html>
<html>
<head>
    <title>Data Schedule</title>
</head>
<body>

    <h1>Data Schedule</h1>

    <a href="/schedules/create">
        Tambah Schedule
    </a>

    <br><br>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Room</th>
            <th>Mata Kuliah</th>
            <th>Dosen</th>
            <th>Hari</th>
            <th>Jam</th>
            <th>Aksi</th>
        </tr>

        @foreach($schedules as $schedule)

        <tr>

            <td>{{ $schedule->schedule_id }}</td>

            <td>
                {{ $schedule->room->nama_ruangan }}
            </td>

            <td>{{ $schedule->mata_kuliah }}</td>

            <td>{{ $schedule->dosen }}</td>

            <td>{{ $schedule->hari }}</td>

            <td>
                {{ $schedule->jam_mulai }}
                -
                {{ $schedule->jam_selesai }}
            </td>

            <td>

                <a href="/schedules/edit/{{ $schedule->schedule_id }}">
                    Edit
                </a>

                |

                <a href="/schedules/delete/{{ $schedule->schedule_id }}"
                onclick="return confirm('Yakin ingin menghapus schedule ini?')">

                    Delete

                </a>

            </td>

        </tr>

        @endforeach

    </table>

</body>
</html>