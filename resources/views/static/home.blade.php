<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>

<div class="container">
  @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
  @elseif ($message = Session::get('error'))
    <div class="alert alert-danger">
        <p>{{ $message }}</p>
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
  <form action="{{ route('store') }}" method="POST">
    @csrf
    <div class="form-group">
      <label>Deal Name</label>
      <input type="text" name='deal_name' class="form-control" placeholder="Enter deal name" required>
    </div>
    <div class="form-group">
      <label>Task Subject</label>
      <input type="text" name='task_subj'class="form-control" placeholder="Enter task name">
    </div>
    <input type="submit" value="Создать" class="btn btn-primary"></input>
  </form>
</div>

</body>
</html>
