<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">Product Form</div>
            <div class="card-body">
                <form id="productForm">
                    <input type="hidden" id="productIndex" value="">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" id="prodName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity in Stock</label>
                        <input type="number" id="qtyInStock" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price per Item</label>
                        <input type="number" step="0.01" id="pricePerItem" class="form-control" required>
                    </div>
                    <button onclick="submitProduct(event)" type="button" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">Submitted Products</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity in Stock</th>
                            <th>Price per Item</th>
                            <th>Datetime Submitted</th>
                            <th>Total Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        @foreach($products as $index => $product)
                        <tr>
                            <td>{{ $product['prodName'] }}</td>
                            <td>{{ $product['qtyInStock'] }}</td>
                            <td>{{ $product['pricePerItem'] }}</td>
                            <td>{{ $product['dateTimeSumitted'] }}</td>
                            <td>{{ $product['totalValue'] }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-index="{{ $index }}">Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">Total</td>
                            <td id="sumTotalValue">{{ array_sum(array_column($products, 'totalValue')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        let products = <?= json_encode($products) ?>
        let editingIndex = null;

        function submitProduct(e) {
            e.preventDefault();

            const prodName = $('#prodName').val();
            const qtyInStock = $('#qtyInStock').val();
            const pricePerItem = $('#pricePerItem').val();
            const index = $('#productIndex').val();

            const url = index ? '/edit' : '/submit';
            const method = index ? 'PUT' : 'POST';
            const data = {
                prodName,
                qtyInStock,
                pricePerItem,
            };

            if (index) data.index = index;

            $.ajax({
                url: url,
                method: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                success: function(response) {
                    if (response.status === 200) {
                        products = response.products;
                        updateTable(products);

                        // Reset the form
                        $('#productForm')[0].reset();
                        $('#productIndex').val('');
                        $('button[type="button"]').text('Submit');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Failed to process the product. Please try again.');
                }
            });
        }

        $(document).on('click', '.edit-btn', function() {
            const index = $(this).data('index');
            const product = products[index];

            // Prefill the form with product details
            $('#prodName').val(product.prodName);
            $('#qtyInStock').val(product.qtyInStock);
            $('#pricePerItem').val(product.pricePerItem);
            $('#productIndex').val(index);

            // Change the button text
            $('button[type="button"]').text('Update');
        });

        function updateTable(products) {
            let tableBody = '';
            let sumTotal = 0;

            products.forEach((product, index) => {
                sumTotal += product.totalValue;
                tableBody += `
                    <tr>
                        <td>${product.prodName}</td>
                        <td>${product.qtyInStock}</td>
                        <td>${product.pricePerItem}</td>
                        <td>${product.dateTimeSumitted}</td>
                        <td>${product.totalValue}</td>
                        <td>
                            <button class="btn btn-sm btn-warning edit-btn" data-index="${index}">Edit</button>
                        </td>
                    </tr>
                `;
            });

            $('#productTableBody').html(tableBody);
            $('#sumTotalValue').text(sumTotal.toFixed(2));
        }
    </script>
</body>

</html>