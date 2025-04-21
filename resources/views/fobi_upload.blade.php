<!DOCTYPE html>
<html>
<head>
    <title>Upload Fobi Data</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Upload Fobi Data</h1>

    <form action="{{ route('fobi.storeChecklistAndFauna') }}" method="POST">
        @csrf
        <h2>Checklist</h2>
        <input type="text" name="latitude" placeholder="Latitude" required>
        <input type="text" name="longitude" placeholder="Longitude" required>
        <input type="number" name="tujuan_pengamatan" placeholder="Tujuan Pengamatan" required>
        <input type="text" name="observer" placeholder="Observer" required>
        <input type="text" name="additional_note" placeholder="Additional Note">
        <input type="number" name="active" placeholder="Active">
        <input type="date" name="tgl_pengamatan" placeholder="Tanggal Pengamatan">
        <input type="time" name="start_time" placeholder="Start Time">
        <input type="time" name="end_time" placeholder="End Time">
        <input type="number" name="completed" placeholder="Completed">

        <h2>Fauna</h2>
        <input type="text" id="fauna_name" placeholder="Fauna Name" required>
        <input type="hidden" name="fauna_id" id="fauna_id">
        <input type="text" name="count" placeholder="Count" required>
        <input type="text" name="notes" placeholder="Notes">
        <input type="number" name="breeding" placeholder="Breeding">
        <input type="text" name="breeding_note" placeholder="Breeding Note">
        <input type="number" name="breeding_type_id" placeholder="Breeding Type ID">

        <button type="submit">Upload Data</button>
    </form>

    <script>
        $(document).ready(function() {
            $('#fauna_name').on('input', function() {
                var faunaName = $(this).val();
                if (faunaName.length > 2) {
                    $.ajax({
                        url: '{{ route("fobi.getFaunaId") }}',
                        type: 'GET',
                        data: { name: faunaName },
                        success: function(data) {
                            if (data.fauna_id) {
                                $('#fauna_id').val(data.fauna_id);
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
