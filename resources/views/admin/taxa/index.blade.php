<!-- resources/views/admin/taxa/index.blade.php -->
<div class="container">
    <h1>Manage Taxa</h1>
    
    <div class="controls">
        <form action="{{ route('admin.taxa.index') }}" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
            <select name="per_page">
                <option value="10" @selected(request('per_page') == 10)>10</option>
                <option value="25" @selected(request('per_page') == 25)>25</option>
                <option value="50" @selected(request('per_page') == 50)>50</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <div class="actions">
            <a href="{{ route('admin.taxa.create') }}" class="btn btn-primary">Add New</a>
            <a href="{{ route('admin.taxa.export') }}" class="btn btn-success">Export CSV</a>
            
            <form action="{{ route('admin.taxa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv_file">
                <button type="submit" class="btn btn-info">Import CSV</button>
            </form>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('admin.taxa.index', ['sort' => 'scientific_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                        Scientific Name
                    </a>
                </th>
                <!-- Add other columns -->
            </tr>
        </thead>
        <tbody>
            @foreach($taxa as $taxon)
                <tr>
                    <td>{{ $taxon->scientific_name }}</td>
                    <!-- Add other columns -->
                    <td>
                        <a href="{{ route('admin.taxa.edit', $taxon) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.taxa.destroy', $taxon) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $taxa->links() }}
</div>
