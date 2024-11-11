/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/ClientSide/javascript.js to edit this template
 */


$(document).ready(function () {
    activateMenu();
    adminActionClick();

    const s = document.querySelector('.Subscribe');
    const b = document.querySelector('button');
    b.addEventListener('click', (e) => {
        e.preventDefault();
        s.classList.toggle('Subscribe--loading');
        setTimeout(() => {
            s.classList.remove('Subscribe--loading');
            s.classList.toggle('Subscribe--complete');
        }, 2000);

        setTimeout(() => {
            s.classList.remove('Subscribe--complete');
        }, 5000);
    });

    $("#btn_subscribe").on('click', function (e) {
        e.preventDefault();
        var mail = $("#mail").val().trim();
        if (!mail) {
            alert("You are required to input your email address");
            return;
        }
        if (!isValidEmail(mail)) {
            alert("Invalid email address");
            return;
        }
        alert("Thank you for subscribing.\nYou will receive a confirmation email shortly.");
        $(this).scrollTop(0);
        window.location.reload();
    });

    function isValidEmail(email) {
        // Regular expression to match an email address
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }






    $('.btn-permission').on('click', function () {
        var id = $(this).attr('id');
        var userRight = '';
        var role = $(this).data("role-id");
        console.log(role);
        if (role == 'Member') {
            userRight = 'admin';
        } else {
            userRight = 'Member';
        }
        $.ajax({
            url: 'ajax/changeMemberPermission.php',
            type: 'POST',
            data: {
                id: id,
                right: userRight
            },
            success: function (response) {
                console.log(response);
            }
        });

    });





});
function activateMenu()
{
    var current_page_URL = location.href;
    $(".navbar-nav a").each(function ()
    {
        var target_URL = $(this).prop("href");
        if (target_URL === current_page_URL)
        {
            $('nav a').parents('li, ul').removeClass('active');
            $(this).parent('li').addClass('active');
            return false;
        }
    });
}

function adminAddClothesButtonAJAX() {
    $('#add_clothes_btn').on('click', function (e) {
        e.preventDefault();
        var c_size = document.getElementsByName("c_size[]");
        var c_size_value = [];

        var c_color = document.getElementsByName("c_color[]");
        var c_color_value = [];

        for (var i = 0; i < c_size.length; i++) {
            if (c_size[i].checked) {
                c_size_value.push(c_size[i].value);
            } else {
                c_size_value = c_size_value.filter(e => e !== c_size[i].value);
            }
        }

        for (var i = 0; i < c_color.length; i++) {
            if (c_color[i].checked) {
                c_color_value.push(c_color[i].value);
            } else {
                c_color_value = c_color_value.filter(e => e !== c_color[i].value);
            }
        }
        var error = '';
        var trigger = true;
        if ($('#c_type').val() == 'Select Clothing Type') {
            error += 'Clothing Type field is required\n';
            trigger = false;
        }
        if ($('#c_name').val() == '') {
            error += 'Clothing Name field is required\n';
            trigger = false;
        }
        if ($('#c_desc').val() == '') {
            error += 'Clothing Description field is required\n';
            trigger = false;
        }
        if (c_size_value.length == 0) {
            error += 'Clothing Size require to select at least 1 option\n';
            trigger = false;
        }
        if (c_color_value.length == 0) {
            error += 'Clothing Color require to select at least 1 option\n';
            trigger = false;
        }
        var price = parseFloat($('#c_price').val());
        if ($('#c_price').val() == '') {
            error += 'Clothing Price field is required\n';
            trigger = false;
        } else if (isNaN(price)) {
            error += 'Clothing Price field is required to be number\n';
            trigger = false;
        }
        if ($('#c_image').val() == '') {
            error += 'Clothing Image field is required\n';
            trigger = false;
        }
        if (trigger == false) {
            alert(error);
        } else {
            var formData = new FormData();
            formData.append('c_name', $('#c_name').val());
            formData.append('c_desc', $('#c_desc').val());
            formData.append('c_size', $('#c_size').val());
            formData.append('c_price', $('#c_price').val());
            formData.append('c_color', $('#c_color').val());
            formData.append('c_color', c_color_value);
            formData.append('c_image', $('#c_image')[0].files[0]);
            formData.append('c_type', $('#c_type').val());
            formData.append('c_size_value', c_size_value);
            $.ajax({
                url: 'ajax/createNewClothes.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
//                    console.log(response);
                    // do something with the response
//                alert('Item added.');
                    console.log(response);
                    alert(response);
                    window.location.reload();
                }
            });
        }



        // The c_size_value array now contains the values of the checked checkboxes
//        console.log(c_size_value);


    });
}

//$('#add_clothes_btn').on('click', function (e) {
//        e.preventDefault();
//        var clothesName = $('#c_name').val();
//        var clothesBrand = $('#c_brand').val();
//        var clothesPrice = $('#c_price').val();
//        var clothesImage = $('#c_image').val();
//        var clothesDescription = $('#c_desc').val();
//        var clothesSize = $('#c_size').val();
//
//        $.ajax({
//            url: 'createNewClothes.php',
//            method: 'POST',
//            data: {
//                c_name: clothesName,
//                c_brand: clothesBrand,
//                c_price: clothesPrice,
//                c_image: clothesImage,
//                c_desc: clothesDescription,
//                c_size: clothesSize
//            },
//            success: function (response) {
//                // Handle the server response
//                console.log(response);
//                alert('Item added.');
//                window.location.reload();
//            },
//            error: function (xhr, status, error) {
//                // Handle the error
//                console.log(xhr.responseText);
//            }
//        });
//    });

function adminEditButtonClicked() {
    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('clothing-id');
//        console.log(id);
        $.ajax({
            url: 'ajax/getClothing.php',
            type: 'POST',
            data: {id: id},
            success: function (response) {
                console.log(response);
                var data = JSON.parse(response);
                var clothing = data.clothing;
                var colors = data.colors;

                $('#e_id').val(clothing[0].clothing_id);
                $('#e_name').val(clothing[0].clothing_title);
                $('#e_desc').val(clothing[0].clothing_description);
                $('#e_price').val(clothing[0].clothing_price);
                $('#e_image_hidden').val(clothing[0].clothing_image);
                $('#e_type').val(clothing[0].clothing_type);

                $('input[id^="e_size_"]').prop('checked', false);
                for (var i = 0; i < clothing.length; i++) {
                    var e_size_checkbox = $('input[id="e_size_' + clothing[i].size_id + '"]');
                    e_size_checkbox.prop('checked', true);
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'e_size_hidden[]',
                        value: clothing[i].size_id
                    }).appendTo('#hidden-inputs-container');
                }
                console.log(colors);
                $('input[id^="e_color_"]').prop('checked', false);
                for (var i = 0; i < colors.length; i++) {
                    var e_colors_checkbox = $('input[id="e_color_' + colors[i].color_id + '"]');
                    e_colors_checkbox.prop('checked', true);
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'e_color_hidden[]',
                        value: colors[i].color_id
                    }).appendTo('#hidden-color-container');
                }
            }
        });
    });
}

function adminEditClothesButtonAJAX() {
    $('#edit_clothes_btn').on('click', function (e) {
        e.preventDefault();

        var e_size = document.getElementsByName("e_size[]");
        var e_size_value = [];
        var e_color = document.getElementsByName("e_color[]");
        var e_color_value = [];
        var e_id = $('#e_id').val();
        var e_size_hidden = document.getElementsByName("e_size_hidden[]");
        var e_size_hidden_value = [];
        var e_color_hidden = document.getElementsByName("e_color_hidden[]");
        var e_color_hidden_value = [];
        var checkResult = 0;

        for (var i = 0; i < e_size.length; i++) {
            if (e_size[i].checked) {
                e_size_value.push(e_size[i].value);
            } else {
                e_size_value = e_size_value.filter(e => e !== e_size[i].value);
            }
        }

        for (var i = 0; i < e_size_hidden.length; i++) {
            e_size_hidden_value.push(e_size_hidden[i].value);
        }

        for (var i = 0; i < e_color.length; i++) {
            if (e_color[i].checked) {
                e_color_value.push(e_color[i].value);
            } else {
                e_color_value = e_color_value.filter(e => e !== e_color[i].value);
            }
        }

        for (var i = 0; i < e_color_hidden.length; i++) {
            e_color_hidden_value.push(e_color_hidden[i].value);
        }

        var errorMessage = '';
        var trigger = true;
        if ($('#e_type').val() == 'Select Clothing Type') {
            errorMessage += 'Clothing Type field is required\n';
            trigger = false;
        }
        if ($('#e_name').val() == '') {
            errorMessage += 'Clothing Name field is required\n';
            trigger = false;
        }
        if ($('#e_desc').val() == '') {
            errorMessage += 'Clothing Description field is required\n';
            trigger = false;
        }
        if (e_size_value.length == 0) {
            errorMessage += 'Clothing Size require to select at least 1 option\n';
            trigger = false;
        }
        if (e_color_value.length == 0) {
            errorMessage += 'Clothing Color require to select at least 1 option\n';
            trigger = false;
        }
        var price = parseFloat($('#e_price').val());
        if ($('#e_price').val() == '') {
            errorMessage += 'Clothing Price field is required\n';
            trigger = false;
        } else if (isNaN(price)) {
            errorMessage += 'Clothing Price field is required to be number\n';
            trigger = false;
        }

        if (trigger == false) {
            alert(errorMessage);
        } else {
            console.log("Size: " + e_size_value);
            console.log("Hidden Size: " + e_size_hidden_value);
            console.log("Color: " + e_color_value);
            console.log("Hidden Color: " + e_color_hidden_value);
            var formData = new FormData();
            formData.append('e_id', $('#e_id').val());
            formData.append('e_name', $('#e_name').val());
            formData.append('e_desc', $('#e_desc').val());
            formData.append('e_type', $('#e_type').val());
            formData.append('e_price', $('#e_price').val());
            formData.append('e_color', $('#e_color').val());
            formData.append('e_image', $('#e_image')[0].files[0]);
            formData.append('e_size_value', e_size_value);
            formData.append('e_size_value_hidden', e_size_hidden_value);
            formData.append('e_color_value', e_color_value);
            formData.append('e_color_value_hidden', e_color_hidden_value);
            formData.append('e_image_hidden', $('#e_image_hidden').val());
            $.ajax({
                url: 'ajax/updateClothing.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log(response);
                    // do something with the response
                    alert('Clothing detail update successfully');
                    window.location.reload();
                }
            });
        }


        // The c_size_value array now contains the values of the checked checkboxes
        //console.log(e_size_hidden_value);

//        $.ajax({
//            url: 'ajax/checkEditSize.php',
//            type: 'POST',
//            data: {
//                e_size: e_size_value,
//                e_id: e_id,
//                e_size_hidden: e_size_hidden_value
//            },
//            success: function (response) {
//                console.log(response);
//                // do something with the response
//                console.log(typeof response);
//                checkResult = parseInt(response);
//            }
//        });
//        console.log(checkResult);


    });
}

function adminDeleteButtonClicked() {
    $(document).on('click', '.btn-delete', function () {
        var id = $(this).attr('id');
//        var img = $(this).attr('img');
        console.log(id);
        $.ajax({
            url: 'ajax/getClothing.php',
            type: 'POST',
            data: {id: id},
            success: function (response) {
                //console.log(response);
                var clothing = JSON.parse(response);

                $('#d_id').val(clothing.clothing[0].clothing_id);
                $('#d_img').val(clothing.clothing[0].clothing_image);
                $('#d_size').val(clothing.clothing[0].clothing_size);
            }
        });
    });
}

function adminDeleteClothesButtonAJAX() {
    $('#delete_clothes_btn').on('click', function () {
        var delete_id = $('#d_id').val();
        var delete_image = $('#d_img').val();
        var delete_size = $('#d_size').val();
        console.log(delete_size);
        $.ajax({
            url: 'ajax/deleteClothing.php',
            method: 'POST',
            data: {
                d_id: delete_id,
                d_img: delete_image,
                d_size: delete_size
            },
            success: function (response) {
                // Handle the server response
                console.log(response);
                alert('Clothing delete successfully');
//                $('#editModel').modal('hide');
                window.location.reload();
            },
            error: function (xhr, status, error) {
                // Handle the error
                console.log(xhr.responseText);
            }
        });
    });
}

function viewAddtoCartButton() {
    $(".add_to_cart").on("click", function (e) {
        var checkLogin = $('.checkLogin').data('check-id');
        var cid = $(this).data('clothing-id');
        var m_role = $(this).data('member-role');
        var m_id = $(this).data('member-id');
//        console.log(mid);
        var p_price = $(this).data('price');
//        console.log(p_price);
        console.log(checkLogin);
        if (checkLogin != "") {
            if (m_role == 'admin') {
                alert("You are not allow to add to cart");
            } else {
                $.ajax({
                    url: 'ajax/checkClothingExists.php',
                    type: 'POST',
                    data: {c_id: cid, m_id: m_id},
                    success: function (response) {
                        if (response.trim() == "exists") {
                            alert("You had already added to cart, please go to 'View Cart' to edit ");
                        } else {
                            $.ajax({
                                url: 'ajax/getSizeByCID.php',
                                type: 'POST',
                                data: {c_id: cid},
                                success: function (response) {
//                   console.log(response);
//                    var sizing = JSON.parse(response);
                                    var data = JSON.parse(response);
                                    var sizing = data.sizing;
                                    var colors = data.colors;
                                    //console.log(sizing);
                                    //console.log(sizing.length);

                                    // Get the select element
                                    var select = $('#size_select');
                                    var color_select = $('#color_select');
                                    // Clear any existing options
                                    select.empty();

                                    // Add each size as an option
                                    for (var i = 0; i < sizing.length; i++) {
                                        var size = sizing[i];
                                        var option = $('<option>');
                                        option.val(size['size_id']);
                                        option.text(size['size_name']);
                                        select.append(option);
                                    }

                                    // Add each size as an option
                                    for (var i = 0; i < colors.length; i++) {
                                        var color = colors[i];
                                        var option = $('<option>');
                                        option.val(color['color_id']);
                                        option.text(color['color_name']);
                                        color_select.append(option);
                                    }
                                    $("#cid").val(cid);
                                    $('.cp_price').val(p_price);
                                    $('#cartModel').modal('show');
                                }, error: function (response) {

                                }
                            });
                        }

                    }, error: function (response) {

                    }
                });

            }
        } else {
            alert("You are require to login to add cart");
        }
    });
}

function addToCartAjax() {
    $("#add_to_cart_ajax").on("click", function () {
        var cid = $('#cid').val();
        var mid = $('#mid').val();
        var sid = $('#size_select').val();
        var color_id = $('#color_select').val();
        var qty = $('#qty').val();
        var price = $('.cp_price').val();
        

        if (parseInt(qty) <= 0) {
            alert("Your quantiy cannot be less than 0");
        } else {
            $.ajax({
                url: 'ajax/addToCart.php',
                type: 'POST',
                data: {
                    c_id: cid,
                    m_id: mid,
                    size_id: sid,
                    quantity: qty,
                    color_id: color_id,
                    price: price
                },
                success: function (response) {
                    console.log(response);
                    // Handle the response from the server here
                    alert(response);
                    window.location.reload();
                }
            });
        }



    });
}

function viewCartDetail() {
    var list = document.getElementById('valueList');
    var text = '<span> total price: </span>';
    var listArray = [];
    var priceCheckBox = document.querySelectorAll('.amtCheckBox');
    let price = 0.0;

    for (var checkbox of priceCheckBox) {
        checkbox.addEventListener('click', function () {
            if (this.checked === true) {
                listArray.push(this.value);
                price = price + parseFloat(this.value);
            } else {
                listArray = listArray.filter(e => e !== this.value);
                price = price - parseFloat(this.value);

            }
            if (listArray.length == 0) {
                price = 0.00;
            }
//           list.innerHTML = text + listArray.join(" / ");
            list.innerHTML = text + "$" + price.toFixed(2);
            $("#hidden_price").val(price);
        });
    }


}

function editButtonCart() {
    $(".btn-edit-cart").on('click', function () {
        var c_id = $(this).data("clothing-id");
        var size_id = $(this).data("size-id");
        var color_id = $(this).data("color-id");
        var mid = $('#mid').val();
        var cart_id = $(this).data("cart-id");

//        console.log(mid);
        //console.log(cart_id);
        //console.log(c_id);
        $.ajax({
            url: 'ajax/getSizeByCidAndMid.php',
            type: 'POST',
            data: {c_id: c_id, size_id: size_id, color_id: color_id},
            success: function (response) {
                //console.log(response);
                var data = JSON.parse(response);
                var sizing = data.sizing;
                var colors = data.colors;
                var clothing = data.clothing;

                //console.log(clothing['qty']);
                //console.log(clothing);

                // Get the select element
                var select = $('#edit_size_select');
                var selectColor = $('#edit_color_select');
                // Clear any existing options
                select.empty();
                selectColor.empty();

                // Add each size as an option
                for (var i = 0; i < sizing.length; i++) {
                    var size = sizing[i];
                    var option = $('<option>');
                    option.val(size['size_id']);
                    option.text(size['size_name']);
                    select.append(option);
                }

                for (var i = 0; i < colors.length; i++) {
                    var color = colors[i];
                    var option = $('<option>');
                    option.val(color['color_id']);
                    option.text(color['color_name']);
                    selectColor.append(option);
                }


                $("#ec_edit_c_id").val(c_id);
                $("#ec_edit_cart_id").val(cart_id);
                $('#old_price').val(clothing['clothing_price']);
                $('#qty').val(clothing['qty']);
                $('#old_qty').val(clothing['qty']);
                $('#old_size').val(clothing['size_id']);
                $('#old_color').val(clothing['color_id']);
                $('#old_c_id').val(clothing['clothing_id']);
                select.val(clothing['size_id']);
                selectColor.val(clothing['color_id']);
                //$('#cartModel').modal('show');
            }, error: function (response) {

            }
        });

//        $.ajax({
//            url: 'ajax/getCart.php',
//            type: 'POST',
//            data: {
//                c_id: c_id,
//                size_id: size_id
//            },
//            success: function (response) {
////                console.log(response);
//                var cart = JSON.parse(response);
////                console.log(cart);
//                $('#qty').val(cart.qty);
//                $('#old_qty').val(cart.qty);
//                var select = $('#edit_size_select');
//                select.val(cart.size_id);
//            }
//        });
    });
}

function editButtonCartAjax() {
    $('#edit_to_cart_ajax').on('click', function () {
        var m_id = $('#mid').val();
        var cart_id = $("#ec_edit_cart_id").val();
        var clothing_id = $('#old_c_id').val();
        var o_price = $('#old_price').val();
        var qty = $('#qty').val();
        var old_qty = $('#old_qty').val();

        // size using update
        var selectSize = $('#edit_size_select').val();
        var old_size = $('#old_size').val();

        var selectColor = $('#edit_color_select').val();
        var old_color = $('#old_color').val();


//        console.log("New Size: " + selectSize);
//        console.log("Old Size: " + old_size);
//
//        console.log("New Color: " + selectColor);
//        console.log("Old Color: " + old_color);
//
//        // check to be delete or not
//        if (qty !== old_qty) {
//            if (qty > old_qty) {
//                // add qty
//                countAdd = qty - old_qty;
//                console.log("trigger 1");
//                console.log("Add: " + countAdd);
//            } else {
//                // delete qty
//                console.log("trigger 2");
//                countRemove = old_qty - qty;
//                console.log("remve: " + countRemove);
//            }
//        } else {
//            // qty is same
//            console.log("no change");
//        }

//        $.ajax({
//            url: 'ajax/updateCart.php',
//            type: 'POST',
//            data: {
//                qty: qty,
//                old_qty: old_qty,
//                size: selectSize,
//                old_size: old_size,
//                color: selectColor,
//                old_color: old_color,
//                m_id: m_id,
//                clothing_id: clothing_id
//            },
//            success: function (response) {
//                alert(response);
//                window.location.reload();
//            }
//        });

        if (parseInt(qty) <= 0) {
            alert("Your quantiy cannot be less than 0");
        } else {
            $.ajax({
                url: 'ajax/updateCart.php',
                type: 'POST',
                data: {
                    qty: qty,
                    old_qty: old_qty,
                    size: selectSize,
                    old_size: old_size,
                    color: selectColor,
                    old_color: old_color,
                    m_id: m_id,
                    clothing_id: clothing_id,
                    cart_id: cart_id,
                    o_price: o_price

                },
                success: function (response) {
                    alert(response);
                    window.location.reload();
                }
            });

        }



        //DELETE FROM cart WHERE clothing_id=11 AND size_id=3 LIMIT 1; SQL limit to delete only 1 or 2
    });
}

function adminActionClick() {
    adminAddClothesButtonAJAX();
    adminEditButtonClicked();
    adminEditClothesButtonAJAX();
    adminDeleteButtonClicked();
    adminDeleteClothesButtonAJAX();

    viewAddtoCartButton();
    addToCartAjax();
    viewCartDetail();

    editButtonCart();
    editButtonCartAjax();

    deleteCartClick();
    deleteCartClickAjax();

    addColorAjax();
    viewColorClick();
    deleteColorClick();

    checkOutButton();

    deliveryButtonClick();

    checkNewPasswordButton();

}

function addColorAjax() {
    $('#add_color_btn').on('click', function () {
        var color = $('#g_color').val();

        var errorMessage = '';
        var trigger = true;
        if (color == '') {
            errorMessage = 'Color is require';
            trigger = false;
        }
        if (trigger == false) {
            alert(errorMessage);
        } else {
            $.ajax({
                url: 'ajax/addColor.php',
                type: 'POST',
                data: {
                    color: color
                },
                success: function (response) {
                    //console.log(response);
                    alert(response);
                    if (response != 'Already have the color') {
                        window.location.reload();
                    }
                }
            });
        }


    });
}

function viewColorClick() {
    $('.btn-viewColor').on('click', function () {
        $.ajax({
            url: 'ajax/getColor.php',
            type: 'POST',
            data: {},
            success: function (response) {
                console.log(response);
                var colors = JSON.parse(response);
                // Create the container element
                const container = document.getElementById('getColor');

                container.innerHTML = '';

                for (var i = 0; i < colors.length; i++) {
                    // Create a new div element to display the color and button
                    const div = document.createElement('div');
                    div.className = 'd-flex align-items-center justify-content-between mb-3';
                    div.innerHTML = '<span>' + colors[i].color_name + '</span>' +
                            '<button class="btn btn-danger btn-delete-color" id="' + colors[i].color_id + '">Delete</button>';

                    // Append the div to the container
                    container.appendChild(div);
                }
            }
        });
    });
}

function deleteColorClick() {
    $('#getColor').on('click', '.btn-delete-color', function () {
        var colorId = $(this).attr('id');
        console.log(colorId);
        // Call a function to delete the color with the specified ID
        $.ajax({
            url: 'ajax/deleteColor.php',
            type: 'POST',
            data: {id: colorId},
            success: function (response) {
                console.log(response);
                alert(response);
                window.location.reload();
            }
        });
    });
}

function deleteCartClick() {
    $('.btn-delete-cart').on('click', function () {
        var clothing_id = $(this).data('c-id');
        var cart_id = $(this).data('cart-id');
        $('#old_hidden_c_id').val(clothing_id);
        $('#old_hidden_cart_id').val(cart_id);

    });
}

function deleteCartClickAjax() {
    $('#delete_cart_btn').on('click', function () {
        var m_id = $('#mid').val();
        var clothing_id = $('#old_hidden_c_id').val();
        var cart_id = $('#old_hidden_cart_id').val();
        console.log(cart_id);
        $.ajax({
            url: 'ajax/deleteCart.php',
            type: 'POST',
            data: {
                m_id: m_id,
                clothing_id: clothing_id,
                cart_id: cart_id
            },
            success: function (response) {
                alert(response);
                window.location.reload();
            }
        });
    });
}

function checkOutButton() {
    $('.btn-check-out').on('click', function () {


        var priceArr = [];
        var nameArr = [];
        var cIDArr = [];
        var qtyArr = [];
        var member_id = $('#mid').val();
        var id = $('.ce_id').val();
        var ceNameDivs = document.querySelectorAll('.ce_name');
        var ceID = document.querySelectorAll('.ce_id');
        var cbCheck = document.getElementsByClassName('amtCheckBox');
        var ceQty = document.querySelectorAll('.ce_qty');
        for (var i = 0; cbCheck[i]; ++i)
        {
            if (cbCheck[i].checked) {
                priceArr.push(cbCheck[i].value);
                nameArr.push(ceNameDivs[i].getAttribute('data-name'));
                cIDArr.push(ceID[i].value);
                qtyArr.push(ceQty[i].getAttribute('data-qty'));
            }
        }
        console.log(qtyArr);

        $.ajax({
            url: 'ajax/updateOrder.php',
            type: 'POST',
            data: {
                member_id: member_id,
                cart_id: cIDArr,
                amt: priceArr,
                name: nameArr,
                qty: qtyArr,
                paypal_id: '52861117PH035631G',
                paypal_time: '2023-03-23T20:25:41Z'
            },
            success: function (response) {
                console.log(response);
                alert(response);
                window.location.reload();
            }
        });


    });

}

function deliveryButtonClick() {
    $('.btn-delivery').on('click', function () {
        var payment_id = $(this).data('payment-id');

        $.ajax({
            url: 'ajax/updateDelivery.php',
            type: 'POST',
            data: {
                payment_id: payment_id
            },
            success: function (response) {
                alert(response);
                window.location.reload();
            }
        });


    });
}

function checkNewPasswordButton() {

    $('#btnResetNewPassword').on('click', function (e) {
        e.preventDefault();
        var errorMsg = document.getElementById("errorMsg");

        var password = $("#pwd").val();
        var cfm_password = $("#cfm_pwd").val();

        $.ajax({
            url: 'ajax/checkPassword.php',
            type: 'POST',
            data: {
                pwd: password,
                cfm_pwd: cfm_password
            },
            success: function (response) {
//                alert(response);
//                window.location.reload();
                if (response.trim() === "ok") {
                    document.getElementById("passwordForm").submit();
                } else {
                    errorMsg.innerHTML  = response;
                }

            }
        });
    });
}
