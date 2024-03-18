<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: #096cd7;
            color: white;
            padding: 10px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: auto;
            /* Center the header */
        }

        .header a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }

        .form-container {
            max-width: 400px;
            margin: auto;
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .export-button {
            margin-top: 10px;
        }

        .header h1 {
            margin: 0;
        }

        .not-action-list {
            padding: 0 !important;
        }

        .list-items img {
            height: 30px;
            width: 30px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div>
            {{-- <h1>Pivot Table</h1>
            <a href="#">Home</a>
            <a href="#">Catalogue</a> --}}
        </div>
        <button class="btn btn-light export-button" onclick="location.href='{{ route('pivot.export') }}'">Export</button>

    </div>

    <div class="container mt-5">
        @if (session('success'))
            <div id="success-message" class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-container">
            <form method="POST" action="{{ route('pivot.store') }}">
                @csrf
                <div class="form-group">
                    <label for="seller">Seller Name:</label>
                    <input type="text" class="form-control" name="seller_name" id="seller_name" placeholder="">
                </div>
                <div class="form-group">
                    <label for="category">Product Category:</label>
                    <select class="form-control" name="product_category" id="category">
                        <option value="Electronics">Electronics</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Books">Books</option>
                        <option value="Home Appliances">Home Appliances</option>
                        <option value="Toys">Toys</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input type="text" class="form-control" name="product_name" id="product_name" placeholder="">
                </div>
                <div class="form-group">
                    <label for="product_description">Product Description:</label>
                    <input type="text" class="form-control" name="product_description" id="product_description"
                        placeholder="">
                </div>
                <div class="form-group">
                    <label for="product_price">Product Price:</label>
                    <input type="text" class="form-control" name="product_price" id="product_price" placeholder="">
                </div>
                <div class="form-group d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div><br>
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center" scope="col">Sr.</th>
                    <th style="text-align: center" scope="col">Seller Name</th>
                    <th style="text-align: center" scope="col">Product Category</th>
                    <th style="text-align: center" scope="col">Product Name</th>
                    <th style="text-align: center" scope="col">Product Description</th>
                    <th scope="col" style="text-align: center">Product Price</th>
                    <th scope="col" style="text-align: center">Action</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $record)
                    <tr>
                        <td style="text-align: center"> {{ $key + 1 }} </td>
                        <td style="text-align: center">{{ $record->seller_name }}</td>
                        <td style="text-align: center">{{ $record->product_category }}</td>
                        <td style="text-align: center">{{ $record->product_name }}</td>
                        <td style="text-align: center">{{ $record->product_description }}</td>
                        <td style="text-align: center">${{ $record->product_price }}</td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <ul class="action-list not-action-list">
                                    <li class="list-items d-flex gap-3 align-items-center">
                                        {{-- <a href="{{ route('pivot.edit', ['id' => $record->id]) }}">
                                            <img src="https://ircc-preprod.novatoresolutions.com/admin/images/Edit_fill.svg"
                                                alt="edit">
                                        </a> --}}
                                        <a onclick="disableDoubleClick()"
                                            href="{{ route('pivot.delete', ['id' => $record->id]) }}">
                                            <img src="https://ircc-preprod.novatoresolutions.com/admin/images/Trash.svg"
                                                alt="Delete">
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        function disableDoubleClick() {
            event.target.classList.add('disabled').event
            event.target.onclick = function() {
                return false;
            };
            return true;
        }
    </script>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $("#success-message").fadeOut("slow");
            }, 5000);
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
