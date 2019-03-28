<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Coalition Technologies skill test</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-6">
            New product:
            <div class="form">
                <label for="#name">Name: </label>
                <input id="name" type="text" title="Product name" name="name"><br />
                <label for="#qty">Quantity: </label>
                <input id="qty" type="number" step="1" title="Quantity in stock" name="qty"><br />
                <label for="#price">Price per item: </label>
                <input id="price" type="number" step="0.01" title="Price per item" name="price"><br />
                <button id="newProduct" class="btn btn-success">Save</button>
            </div>
        </div>

        <div class="col-lg-6">
            <div id="editProduct" class="form d-none">
                Edit product:
                <input type="hidden" id="editid">
                <label for="#editname">Name: </label>
                <input id="editname" type="text" title="Product name" name="editname"><br />
                <label for="#editqty">Quantity: </label>
                <input id="editqty" type="number" step="1" title="Quantity in stock" name="editqty"><br />
                <label for="#editprice">Price per item: </label>
                <input id="editprice" type="number" step="0.01" title="Price per item" name="editprice"><br />
                <button id="edit" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-">
            <table class="table table-striped">
                <tr>
                    <th>Product name</th>
                    <th>Quantity in stock</th>
                    <th>Price per item</th>
                    <th>Datetime submitted</th>
                    <th>Total value number</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">Sum</td>
                    <td id="sum"></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {
        update();
    });

    var products = [];

    $('#newProduct').click(function () {
        $.post({
            url: 'api/newProduct',
            data: {
                name: $('input[name=name]').val(),
                qty: $('input[name=qty]').val(),
                price: $('input[name=price]').val(),
            },
            success: function () {
                update();
            }
        })
    });

    $('#edit').click(function () {
        $.post({
            url: 'api/editProduct/' + $('#editid').val(),
            data: {
                name: $('input[name=editname]').val(),
                qty: $('input[name=editqty]').val(),
                price: $('input[name=editprice]').val(),
            },
            success: function () {
                $('#editProduct').addClass('d-none');
                update();
            }
        })
    });

    function edit(id)
    {
        $('#editid').val(id);
        $('#editname').val(products[id].name);
        $('#editqty').val(products[id].qty);
        $('#editprice').val(products[id].price);

        $('#editProduct').removeClass('d-none');
    }

    function update()
    {
        $('tr[data-id]').remove();

        $.get({
            url: 'api/listProducts',
            success: function (response) {
               var product;
               var sum = 0;
               products = response;

               for (id in response) {
                   if (response.hasOwnProperty(id)) {
                       product = response[id];

                       sum += product.qty * product.price;

                       $('tr:last()').before('<tr data-id="' + id + '">');
                       $('tr[data-id=' + id + ']')
                           .append('<td>' + product.name + '</td>')
                           .append('<td>' + product.qty + '</td>')
                           .append('<td>' + product.price + '</td>')
                           .append('<td>' + product.time + '</td>')
                           .append('<td>' + product.qty * product.price + '</td>')
                           .append('<td><a href="#" class="btn btn-primary" onclick="edit(\'' + id + '\')">Edit</a></td>')
                           .append('</tr>')
                       ;
                   }
               }

               $('#sum').html(sum);
            }
        })
    }
</script>
</body>
</html>
