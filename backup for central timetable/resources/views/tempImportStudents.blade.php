<form action="{{ route('importstudents') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" accept=".xlsx, .xls">
    <button type="submit">Import</button>
</form>
