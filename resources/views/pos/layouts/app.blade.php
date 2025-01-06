<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="text/css" rel="stylesheet" href="{{ asset('admin/app-assets/css/pos/bootstrap.css') }}">
    <link type="text/css" rel="stylesheet" href="{{asset('admin/app-assets/css/pos/bootstrap-vue.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/extensions/toastr.min.css') }}">
    <script src="{{ asset('admin/app-assets/vendors/js/vendors.min.js') }}"></script>

    <script src="{{ asset('admin/app-assets/vendors/js/ui/jquery.sticky.js') }}"></script>
    <script src="{{asset('admin/app-assets/js/pos/popper.js')}}"></script>
    <script src="{{asset('admin/app-assets/js/pos/bootstrap.js')}}"></script>
    <title>POS</title>
    <style>
        .new-button {
            color: darkred;
            background-color: white;
            border-color: darkred;
            font-weight: 600;
        }

        .new-button.active {
            color: white;
            background-color: darkred;
            border-color: darkred;
        }

        .new-table-header {
            color: darkred;
        }

        .table thead th {
            border-bottom: unset;
        }

        .table td, .table th {
            border-top: unset;
        }

        .new-table-row {
            border: 1px solid #afafaf;
            margin-bottom: 2px;
            cursor: pointer;
        }

        .blank-row {
            height: 10px;
        }

        .table-scroll {
            display: block;
            empty-cells: show;
        }

        .table-scroll thead {
            display: block;
            width: 100%;
        }

        .table-scroll tbody {
            display: block;
            position: relative;
            width: 100%;
            max-height: 70vh;
            overflow-y: scroll;
            /*scrollbar-width: none;*/
        }

        .table-scroll tbody::-webkit-scrollbar {
            width: 3; /* Chrome & Edge */
        }

        .table-scroll tr {
            width: 100%;
            display: flex;
        }

        .table-scroll td, .table-scroll th {
            flex-basis: 100%;
            flex-grow: 2;
            display: block;
            padding: 1rem;
            text-align: left;
        }

        .phone-input {
            border: 2px deeppink solid;
        }

        .phone-input::placeholder {
            color: deeppink;
        }

        .product-info {
            border: 2px deeppink solid;
            background-color: #dedede;
            border-radius: 5px;
        }

        .input-group-append, .input-group-prepend {
            display: unset;
        }

        .dropdown-menu.show {
            font-size: smaller
        }

        .discount-button {
            color: gray;
            background-color: #dedede;
            border-color: deeppink;
            font-weight: 600;
            width: 200px;
        }

        .pause-button {
            color: deeppink;
            background-color: white;
            border-color: deeppink;
            font-weight: 600;
            width: 200px;
        }

        .payment-button {
            background-color: deeppink;
            color: white;
            width: 100%;
            border-radius: 10px;
        }

        .header-gap {
            padding-top: 70px
        }

        .customerName {
            color: rgba(0, 101, 197, 1);
        }

        .saveButton {
            background-color: deeppink;
            color: white;
        }

        .customerInfo {
            border-radius: 5px;
            border: 1px solid black;
            cursor: pointer;
            background-color: white;
            margin-bottom: 5px
        }

        .outStock {
            color: darkred;
        }

        .inStock {
            color: forestgreen;
        }

        .discount-type-button {
            background-color: white;
            color: black;
            border-radius: 7px;
        }

        .discount-type-button:hover {
            border: 2px solid deeppink;
        }

        .selected-discount-type {
            background-color: deeppink;
            color: white;
        }

        .selected-discount-type:hover {
            color: white;
        }

        .payCard {
            border-radius: 7px;
            border: 1px solid #dedede;
            box-shadow: rgba(60, 64, 67, 0.3) 0 1px 2px 0, rgba(60, 64, 67, 0.15) 0 2px 6px 2px;
        }

        .modal-dialog {
            min-width: 800px;
        }

        .invoiceNumber {
            padding: 5px;
            background-color: deeppink;
            color: white;
            font-weight: 600;
            font-size: 18px;
            border-radius: 10px;
        }

        .productName {
            font-size: 18px;
            font-weight: 600;
        }

        .smallFont {
            font-size: 12px !important;
        }

    </style>
</head>
<body style="height: 90%; overflow: hidden; padding-right: 20px">
<section style="position:relative;">
    <div id="vue_app">
        @include('pos.partials.header')
        @yield('content')
    </div>

</section>
<script src="{{ asset('admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<script src="{{ asset('vue-js/vue/dist/vue.js') }}"></script>
<script src="{{ asset('admin/app-assets/js/pos/bootstrap-vue.js') }}"></script>
<script src="{{ asset('vue-js/axios/dist/axios.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#customer').hide()
        $('#order').hide()
        $('#pre-order').hide()
        new Vue({
            el: '#vue_app',
            data: {
                config: {
                    get_all_orders_url: "{{ url('pos-orders') }}",
                    get_all_products_url: "{{ url('pos-products') }}",
                    get_all_categories_url: "{{ url('pos-categories') }}",
                    get_all_customers_url: "{{ url('pos-customers') }}",
                    get_customer_url: "{{ url('pos-customer-by-number') }}",
                    get_coupon_code_value_url: "{{ url('pos-coupon-code-discount') }}",
                    store_sell_url: "{{ route('pos.store') }}",
                    store_customer_url: "{{ route('pos.add.customer') }}",
                    update_customer_url: "{{ url('pos-update-customer') }}",
                    print_invoice_url: "{{ url('pos-invoice-print') }}",
                    get_product_by_search_string_url: "{{ url('pos-product-by-name-sku-bar-code') }}",
                    store_pre_order_url: "{{ url('pos-pre-order') }}",
                    get_all_pre_orders_url: "{{ url('pos-pre-orders') }}",
                },
                categories: [],
                products: [],
                customers: [],
                orders: [],
                selectedProducts: [],
                selected_category: '',
                search_string: '',
                total_discount_type: '',
                total_discount_value: 0,
                total_discount_amount: 0,
                special_discount_value: 2,
                special_discount_amount: 0,
                modalShow: false,
                customerNumber: '',
                customer: {},
                currentActiveMenu: 'home',
                selectedInvoice: {},
                couponCode: '',
                couponModalShow: false,
                paymentMenuShow: false,
                change: 0,
                paymentMethods: [{
                    amount: 0,
                    method: 'cash'
                }],
                couponCodeDiscountType: '',
                couponCodeDiscountValue: 0,
                couponCodeDiscountAmount: 0,
                minimumPurchaseAmount: 0,
                couponCodeDiscountShowValue: '',
                newCustomer: {
                    name: '',
                    mobile: '',
                    address: '',
                    email: '',
                    date_of_birth: '',
                    date_of_anniversary: '',
                },
                editableCustomerId: null,
                onHoldIdentifier: '',
                holdOrders: [],
                selectedOnHoldOrderToPos: null,
                preOrderValues: {
                    delivery_date: new Date(),
                    order_from: '',
                    advance_payment: 0,
                    paid_by: '',
                    comment: ''
                },
                pre_orders: [],
                selectedPreOrderId: null,
                waiter_id: '',
                selectedSpecialDiscount: false,
                customer_search_string: '',
                isDisabled: false
            },
            mounted() {
                this.getAllProducts();
                this.getAllCategories();
                this.getCustomers();
                this.getAllOrders();
                // this.getAllPreOrders();
            },
            computed: {
                total_items: function () {
                    return this.selectedProducts.reduce((total, item) => {
                        return total + item.quantity
                    }, 0)
                },
                total_bill: function () {
                    return this.selectedProducts.reduce((total, item) => {
                    }, 0)
                },
                // total_payable_bill: function () {
                //     var vm = this
                //     return (this.total_bill - vm.total_discount_amount - vm.couponCodeDiscountAmount - vm.special_discount_amount)
                // },
                total_payable_bill: function () {
                    var vm = this
                    return Math.round(this.total_bill - vm.couponCodeDiscountAmount - this.allDiscountAmount)
                },
                total_due: function () {
                    return this.total_payable_bill
                },
                total_paying: function () {
                    return this.paymentMethods.reduce((total, item) => {
                        return Number(total) + Number(item.amount)
                    }, 0)
                },
                pay_left: function () {
                    return this.total_paying > this.total_due ? 0 : this.total_due - this.total_paying
                },
                cash_change: function () {
                    return this.total_paying > this.total_due ? this.total_paying - this.total_due : 0
                },
                productWiseDiscount: function () {
                    return this.selectedProducts.reduce((total, item) => {
                        return total + Number(item.discountAmount)
                    }, 0)
                },
                allDiscountAmount: function () {
                    var vm = this
                    return Number(vm.total_discount_amount) + Number(vm.special_discount_amount) + Number(this.productWiseDiscount) + Number(this.membership_discount_amount)
                },
                selectedNotDiscountableProduct: function () {
                    return this.selectedProducts.some(item => !item.discountable);
                },
                membership_discount_amount: function () {
                    var vm = this
                    return vm.customer && vm.total_bill > vm.customer.minimum_purchase ? (Number(vm.total_bill) * Number(vm.customer.purchase_discount) / 100) : 0
                },
                vatTotal: function () {
                    return  Math.round(this.selectedProducts.reduce((total, item) => {
                        let result = this.baseprice(item);
                        return total + ((item.quantity * result.vat))
                    }, 0))
                },
                taxableAmount: function () {
                    return (this.total_payable_bill - this.vatTotal);
                },
                withoutIndivitualDiscountAmount: function () {
                    var vm = this
                    return Number(vm.total_discount_amount) + Number(vm.special_discount_amount) + Number(this.membership_discount_amount);
                },
                discountFromGrandTotal: function () {
                    var vm = this
                    let totalItem = vm.selectedProducts.filter(item => item.quantity > 0).length;
                    let discountedPriceItemWise = 0;
                    if(vm.withoutIndivitualDiscountAmount > 0){
                        discountedPriceItemWise = Number(vm.withoutIndivitualDiscountAmount) / Number(totalItem) 
                    }
                    return discountedPriceItemWise;
                },

            },
            methods: {
                getAllOrders() {
                    var vm = this;
                    axios.get(this.config.get_all_orders_url)
                        .then(function (response) {
                            vm.orders = (response.data);
                        }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                getAllProducts() {
                    var vm = this;
                    axios.get(this.config.get_all_products_url + '?category=' + vm.selected_category + '&search_term=' + vm.search_string)
                        .then(function (response) {
                            vm.products = (response.data);
                        }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                getAllCategories() {
                    var vm = this;
                    axios.get(this.config.get_all_categories_url)
                        .then(function (response) {
                            vm.categories = (response.data);
                        }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                selectProductToSell(product) {
                    var vm = this;
                    var product_id = product.id;
                    if (! product.stock > 0) {
                        toastr.warning('No Stock Available')
                        return false;
                    }
                    var alreadySelected = vm.selectedProducts.some(function (product) {
                        if (product.id === product_id) {
                            product.quantity++
                            if (Number(product.quantity) >= product.stock) {
                                product.quantity = product.stock
                            }
                            product.total = (product.quantity * product.price)
                        }
                        return product.id === product_id
                    });
                    if (!alreadySelected) {
                        vm.selectedProducts.push({
                            id: product.id,
                            name: product.name,
                            quantity: 1,
                            price: product.price,
                            total: product.price,
                            editForm: false,
                            stock: product.stock,
                            discountType: '',
                            discountValue: 0,
                            discountAmount: 0,
                            discountable: product.discountable,
                            vat: product.vat,
                            total_price_with_vat : product.total_price,
                            base_price : product.base_price,
                            vat_type : product.vat_type,
                            vat_amount : product.vat_amount,
                            vat_discount_type : product.discount_type,
                        })
                    }
                    vm.updateDiscount()
                    if (vm.selectedSpecialDiscount) {
                        vm.addSpecialDiscount(false)
                    }
                },
                delete_selected_product: function (row) {
                    this.selectedProducts.splice(this.selectedProducts.indexOf(row), 1);
                    this.updateDiscount()
                    if (this.selectedSpecialDiscount) {
                        this.addSpecialDiscount(false)
                    }
                    let vm = this
                    if (this.selectedProducts.length < 1) {
                        vm.customer = {};
                        vm.discount_type = '';
                        vm.customerNumber = '';
                        vm.total_discount_type = '';
                        vm.total_discount_value = 0;
                        vm.total_discount_amount = 0;
                        vm.special_discount_amount = 0;
                        this.allDiscountAmount = 0;
                        vm.couponCodeDiscountValue = 0;
                        vm.couponCodeDiscountAmount = 0;
                        vm.selectedSpecialDiscount = false;
                    }

                },
                setDiscountType(discountType) {
                    this.total_discount_type = discountType
                },
                updateDiscount() {
                    var vm = this
                    if (this.total_discount_type === 'fixed') {
                        vm.total_discount_amount = vm.total_discount_value
                    }
                    if (this.total_discount_type === 'percentage') {
                        vm.total_discount_amount = (vm.total_bill * vm.total_discount_value) / 100
                    }
                    vm.total_discount_amount = Math.round(vm.total_discount_amount)
                    vm.closeDiscountModal()
                },
                clickedOnCategory(category) {
                    this.selected_category = category
                    this.getAllProducts()
                },
                modalClick() {
                    this.discountModal == true ? this.discountModal = false : true
                    if (this.discountModal) {
                        $('#exampleModalCenter').modal('show');
                    } else {
                        $('#exampleModalCenter').modal('hide');
                    }
                },
                getCustomers() {
                    var vm = this;
                    axios.get(this.config.get_all_customers_url, {
                        params: {
                            search_string: vm.customer_search_string
                        }
                    })
                        .then(function (response) {
                            vm.customers = (response.data);
                        }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                getCustomerInfo() {
                    var vm = this;
                    vm.customer = {}
                    axios.get(this.config.get_customer_url, {
                        params: {
                            number: this.customerNumber
                        }
                    }).then(function (response) {
                        vm.customer = (response.data);
                    }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                submitOrder() {
                    if (this.selectedProducts.length < 1) {
                        toastr.error('No Product Added to Sell', {
                            closeButton: true,
                            progressBar: true,
                        });
                    } else {
                        var vm = this;
                        vm.isDisabled = true
                        axios.post(this.config.store_sell_url, {
                            products: this.selectedProducts,
                            discount_type: this.total_discount_type,
                            discount_value: this.total_discount_value,
                            sub_total: this.total_bill,
                            discount: this.allDiscountAmount,
                            grand_total: this.total_payable_bill,
                            customer_number: this.customerNumber,
                            payment_methods: this.paymentMethods,
                            pre_order_id: this.selectedPreOrderId,
                            waiter_id: this.waiter_id,
                            membership_discount_percentage: vm.customer ? vm.customer.purchase_discount : 0,
                            membership_discount_amount: vm.membership_discount_amount,
                            special_discount_value: vm.special_discount_value,
                            special_discount_amount: vm.special_discount_amount,
                            couponCode: vm.couponCode,
                            couponCodeDiscountType: vm.couponCodeDiscountType,
                            couponCodeDiscountValue: vm.couponCodeDiscountValue,
                            couponCodeDiscountAmount: vm.couponCodeDiscountAmount,
                            total_discount_type: vm.total_discount_type,
                            total_discount_value: vm.total_discount_value,
                            total_discount_amount: vm.total_discount_amount,
                        }).then(function (response) {
                            vm.selectedProducts = [];
                            vm.customer = {};
                            vm.discount_type = '';
                            vm.customerNumber = '';
                            vm.total_discount_type = '';
                            vm.total_discount_value = 0;
                            vm.total_discount_amount = 0;
                            vm.special_discount_amount = 0;
                            this.allDiscountAmount = 0;
                            vm.couponCodeDiscountValue = 0;
                            vm.couponCodeDiscountAmount = 0;
                            vm.getAllProducts()
                            vm.getAllOrders()
                            vm.paymentMenuShow = false
                            vm.paymentMethods = [{
                                amount: 0,
                                method: 'cash'
                            }]
                            toastr.success('Success', {
                                closeButton: true,
                                progressBar: true,
                            });
                            if (vm.selectedOnHoldOrderToPos) {
                                vm.holdOrders.splice(vm.holdOrders.indexOf(vm.selectedOnHoldOrderToPos), 1);
                                sessionStorage.setItem('holdOrder', JSON.stringify(vm.holdOrders))
                            }
                            vm.isDisabled = false
                            vm.printInvoice(response.data.sale.id)
                        }).catch(function (error) {
                            toastr.error(error?.response?.data?.message, {
                                closeButton: true,
                                progressBar: true,
                            });
                            vm.isDisabled = false
                            return false;
                        });
                    }

                },
                changeToNav(navMenu) {
                    let posElement = $('#pos-page')
                    let customerElement = $('#customer')
                    let orderElement = $('#order')
                    let preOrderElement = $('#pre-order')
                    posElement.hide()
                    customerElement.hide()
                    orderElement.hide()
                    preOrderElement.hide()
                    this.currentActiveMenu = navMenu
                    switch (navMenu) {
                        case 'home':
                            posElement.show()
                            break;
                        case 'customers':
                            customerElement.show()
                            break;
                        case 'orders':
                            orderElement.show()
                            break;
                        case 'pre_orders':
                            preOrderElement.show()
                            break;
                        default:
                            this.currentActiveMenu = 'home'
                            posElement.hide()
                            customerElement.hide()
                            orderElement.hide()
                            preOrderElement.hide()
                    }

                },
                addToSelectedInvoice(invoice) {
                    this.selectedInvoice = invoice
                    console.log(this.selectedInvoice)
                },
                addMorePaymentMethod() {
                    this.paymentMethods.push({amount: 0, method: ''})
                },
                checkAvail(index) {
                    let currentMethod = this.paymentMethods[index].method
                    let count = this.paymentMethods.filter(function (method) {
                        return currentMethod == method.method
                    })
                    if (count.length > 1) {
                        toastr.error(currentMethod + ' is already selected', {
                            closeButton: true,
                            progressBar: true,
                        });
                        this.paymentMethods[index].method = ''
                        return
                    }
                },
                updateQuantity(item, update_type) {
                    vm = this
                    this.selectedProducts.some(function (product) {
                        if (product.id === item.id) {
                            if (update_type === 'add') {
                                product.quantity++
                                if (Number(product.quantity) >= product.stock) {
                                    product.quantity = product.stock
                                } else {
                                    product.quantity++
                                }
                            } else if (update_type === 'sub') {
                                if (Number(product.quantity) < 1) {
                                    product.quantity = 1
                                } else {
                                    product.quantity--
                                }
                            } else {
                                if (Number(product.quantity) >= product.stock) {
                                    product.quantity = product.stock
                                } else if (Number(product.quantity) < 1) {
                                    // product.quantity = 1
                                } else {
                                    product.quantity = item.quantity
                                }
                            }
                            product.total = (product.quantity * product.price)
                            // vm.updatePrice()
                        }
                    });
                },
                updatePrice() {
                    this.selectedProducts.some(function (product) {
                        product.total = (product.quantity * product.price)
                    });
                },
                deletePaymentMethod: function (row) {
                    this.paymentMethods.splice(this.paymentMethods.indexOf(row), 1);
                },
                getProductBySearchString() {
                    var vm = this;
                    axios.get(this.config.get_product_by_search_string_url + '?search_term=' + vm.search_string)
                        .then(function (response) {
                            vm.getAllProducts()
                            // if (response.data !== 'multiple') {
                            //     vm.selectProductToSell(response.data)
                            //     vm.search_string = ''
                            //     vm.getAllProducts()
                            // } else {
                            //     vm.getAllProducts()
                            // }
                        }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                getPointRedeemField() {
                    const vm = this
                    this.paymentMenuShow = true
                    if (vm.total_payable_bill > 100) {
                        this.openPaymentModal()
                        var amount = parseInt(this.customer.current_point / 100)
                        this.paymentMethods.push({amount: 100, method: 'point'})
                    } else {
                        toastr.error('Bill must me more than TK.100');
                    }
                },
                getCouponDiscountValue() {
                    var vm = this;
                    axios.get(this.config.get_coupon_code_value_url + '?code=' + vm.couponCode + '&user=' + vm.customerNumber)
                        .then(function (response) {
                            const responseData = response.data
                            if (responseData.success === false) {
                                toastr.error(responseData.message, {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            } else {
                                if (responseData.data.minimum_purchase && responseData.data.minimum_purchase > vm.total_payable_bill) {
                                    toastr.error('Minimum Purchase Amount is ' + responseData.data.minimum_purchase, {
                                        closeButton: true,
                                        progressBar: true,
                                    });
                                }
                                vm.couponCodeDiscountType = responseData.data.discount_type
                                vm.couponCodeDiscountValue = responseData.data.discount_value
                                vm.minimumPurchaseAmount = responseData.data.minimum_purchase
                                if (vm.couponCodeDiscountType === 'fixed') {
                                    vm.couponCodeDiscountAmount = vm.couponCodeDiscountValue
                                    vm.couponCodeDiscountShowValue = vm.couponCodeDiscountValue + ' TK'
                                }
                                if (vm.couponCodeDiscountType === 'percentage') {
                                    vm.couponCodeDiscountAmount = (vm.total_bill * vm.couponCodeDiscountValue) / 100
                                    vm.couponCodeDiscountShowValue = vm.couponCodeDiscountValue + ' %'
                                }
                                vm.couponModalShow === true ? vm.couponModalShow = false : true
                            }

                        }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                submitCustomerInfo() {
                    let url;
                    var vm = this;
                    if (vm.editableCustomerId) {
                        url = vm.config.update_customer_url + '/' + vm.editableCustomerId;
                    } else {
                        url = vm.config.store_customer_url;
                    }
                    axios.post(url, vm.newCustomer).then(function (response) {
                        vm.newCustomer.name = ''
                        vm.newCustomer.email = ''
                        vm.newCustomer.mobile = ''
                        vm.newCustomer.address = ''
                        vm.newCustomer.dob = ''
                        vm.newCustomer.doa = ''
                        toastr.success(response.data.message, {
                            closeButton: true,
                            progressBar: true,
                        });
                        vm.getCustomers()
                    }).catch(function (error) {
                        if (error.response.status === 422) {
                            let errorData = error.response.data.errors
                            $.each(errorData, function (key, val) {
                                toastr.error(val[0], {
                                    closeButton: true,
                                    progressBar: true,
                                });
                            })
                        } else {
                            toastr.error('Something Went Wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                        }

                        return false;
                    });
                },
                editCustomer(customer) {
                    var vm = this
                    vm.editableCustomerId = customer.id
                    vm.newCustomer.name = customer.name
                    vm.newCustomer.email = customer.email
                    vm.newCustomer.mobile = customer.mobile
                    vm.newCustomer.address = customer.address
                    vm.newCustomer.dob = customer.dob
                    vm.newCustomer.doa = customer.doa
                },
                printInvoice(order_id) {
                    this.closePaymentModal()
                    const url = this.config.print_invoice_url + '/' + order_id
                    // window.open(url, '_blank').focus();
                    var printWindow = window.open(url, '_blank');

                    // Wait for the PDF to load and then print
                    printWindow.onload = function () {
                        printWindow.print();
                    };
                    // window.location.href = url
                },
                openCouponModal() {
                    var vm = this
                    vm.$refs['coupon-modal'].show()
                },
                closeCouponModal() {
                    var vm = this
                    vm.$refs['coupon-modal'].hide()
                },
                openDiscountModal() {
                    var vm = this
                    if (vm.productWiseDiscount > 0) {
                        if (confirm("Single Product Discount Applied, Sure to add Total Discount ?")) {
                            vm.$refs['discount-modal'].show()
                        } else {
                            return false;
                        }
                    } else {
                        vm.$refs['discount-modal'].show()
                    }

                },
                closeDiscountModal() {
                    var vm = this
                    vm.$refs['discount-modal'].hide()
                },
                addSpecialDiscount(computed = true) {
                    var vm = this
                    if (vm.productWiseDiscount > 0) {
                        if (confirm("Single Product Discount Applied, Sure to add Total Discount ?")) {
                            if (vm.selectedSpecialDiscount && computed) {
                                vm.selectedSpecialDiscount = false
                                vm.special_discount_amount = 0
                            } else {
                                vm.selectedSpecialDiscount = true
                                vm.special_discount_amount = (vm.total_bill * vm.special_discount_value) / 100
                            }
                            vm.special_discount_amount = Math.round(vm.special_discount_amount)
                        } else {
                            return false;
                        }
                    } else {
                        if (vm.selectedSpecialDiscount && computed) {
                            vm.selectedSpecialDiscount = false
                            vm.special_discount_amount = 0
                        } else {
                            vm.selectedSpecialDiscount = true
                            vm.special_discount_amount = (vm.total_bill * vm.special_discount_value) / 100
                        }
                        vm.special_discount_amount = Math.round(vm.special_discount_amount)
                    }

                },
                updateProductDiscount(sp) {
                    var vm = this
                    if (vm.total_discount_amount > 0) {
                        if (confirm("Total Discount Applied, Sure to add Total Discount Single Product Discount?")) {
                            this.selectedProducts.some(function (product) {
                                var total_cost = (product.quantity * product.price);
                                if (product.discountType == 'p') {
                                    product.discountAmount = (total_cost * product.discountValue) / 100;
                                }
                                if (product.discountType == 'f') {
                                    product.discountAmount = product.discountValue;
                                }
                                product.discountAmount = Math.round(product.discountAmount);
                            });
                        } else {
                            sp.discountType = ''
                            sp.discountValue = 0
                            return false;
                        }
                    } else {
                        this.selectedProducts.some(function (product) {
                            var total_cost = (product.quantity * product.price);
                            if (product.discountType == 'p') {
                                product.discountAmount = (total_cost * product.discountValue) / 100;
                            }
                            if (product.discountType == 'f') {
                                product.discountAmount = product.discountValue;
                            }
                            product.discountAmount = Math.round(product.discountAmount);
                        });
                    }

                },
                openPaymentModal() {
                    var vm = this
                    vm.$refs['payment-modal'].show()
                },
                closePaymentModal() {
                    const vm = this;
                    vm.$refs['payment-modal'].hide()
                },
                openOnHoldModal() {
                    const vm = this;
                    vm.$refs['on-hold-modal'].show()
                },
                closeOnHoldModal() {
                    const vm = this;
                    vm.$refs['on-hold-modal'].hide()
                },
                storeHoldOrder() {
                    const vm = this;
                    let identifierExists = false
                    let currentHoldOrders = []
                    if (sessionStorage.getItem('holdOrder')) {
                        currentHoldOrders = JSON.parse(sessionStorage.getItem('holdOrder'))
                        currentHoldOrders.some(function (order) {
                            if (order.identifier == vm.onHoldIdentifier) {
                                toastr.error('Identifier Already Exists')
                                identifierExists = true
                            }

                        });
                    }
                    if (!identifierExists) {
                        let holdOrder = {
                            'identifier': vm.onHoldIdentifier,
                            'items': vm.selectedProducts,
                            'total': vm.total_items
                        };
                        currentHoldOrders.push(holdOrder)
                        sessionStorage.setItem('holdOrder', JSON.stringify(currentHoldOrders))
                        vm.selectedProducts = []
                        vm.onHoldIdentifier = ''
                        vm.total_discount_type = '';
                        vm.total_discount_value = 0;
                        vm.total_discount_amount = 0;
                        vm.special_discount_amount = 0;
                        this.allDiscountAmount = 0;
                        this.closeOnHoldModal()
                    }

                },
                openOnHoldOrderModal() {
                    var vm = this
                    if (sessionStorage.getItem('holdOrder')) {
                        vm.holdOrders = JSON.parse(sessionStorage.getItem('holdOrder'))
                    }
                    vm.$refs['on-hold-order-modal'].show()
                },
                closeOnHoldOrderModal() {
                    var vm = this
                    vm.$refs['on-hold-order-modal'].hide()
                },
                addHoldOrderToPos(holdOrder) {
                    const vm = this;
                    vm.selectedOnHoldOrderToPos = holdOrder
                    vm.selectedProducts = holdOrder.items
                    this.closeOnHoldOrderModal()
                },
                openPreOrderModal() {
                    var vm = this
                    if (!this.customerNumber) {
                        toastr.error('Please Enter Customer NUmber', {
                            closeButton: true,
                            progressBar: true,
                        });
                        return
                    }
                    if (this.selectedProducts.length < 1) {
                        toastr.error('No Product Added', {
                            closeButton: true,
                            progressBar: true,
                        });
                        return
                    }
                    vm.$refs['pre-order-modal'].show()
                },
                closePreOrderModal() {
                    var vm = this
                    vm.$refs['pre-order-modal'].hide()
                },
                storePreOrder() {
                    if (this.selectedProducts.length < 1) {
                        toastr.error('No Product Added to Sell', {
                            closeButton: true,
                            progressBar: true,
                        });
                        return
                    } else {
                        var vm = this;
                        axios.post(this.config.store_pre_order_url, {
                            products: this.selectedProducts,
                            customer_number: this.customerNumber,
                            order_data: this.preOrderValues,
                            sub_total: this.total_bill,
                            discount: this.total_discount_amount,
                            grand_total: this.total_payable_bill,
                        }).then(function (response) {
                            vm.selectedProducts = [];
                            vm.customer = {};
                            vm.customerNumber = '';
                            vm.getAllProducts()
                            vm.getAllPreOrders()
                            toastr.success('Success', {
                                closeButton: true,
                                progressBar: true,
                            });

                            vm.closePreOrderModal()

                            // vm.printInvoice(response.data.sale.id)
                        }).catch(function (error) {
                            console.log(error)
                            toastr.error('Something Went Wrong', {
                                closeButton: true,
                                progressBar: true,
                            });
                            return false;
                        });
                    }
                },
                getAllPreOrders() {
                    var vm = this;
                    axios.get(this.config.get_all_pre_orders_url)
                        .then(function (response) {
                            vm.pre_orders = (response.data);
                        }).catch(function (error) {
                        toastr.error(error, {
                            closeButton: true,
                            progressBar: true,
                        });
                        return false;
                    });
                },
                transferToSell(order) {
                    const vm = this;
                    // toastr.warning('Under Construction', {
                    //     closeButton: true,
                    //     progressBar: true,
                    // });
                    vm.selectedPreOrderId = order.id
                    order.items.forEach(function (item) {
                        vm.selectedProducts.push({
                            id: item.product.id,
                            name: item.product.name,
                            quantity: item.quantity,
                            price: item.unit_price,
                            total: item.quantity * item.unit_price,
                            editForm: false,
                            stock: item.quantity,
                            discountType: '',
                            discountValue: 0,
                            discountAmount: 0,
                            vat: item.vat,
                            total_price_with_vat : item.total_price,
                            base_price : item.base_price,
                            vat_type : item.vat_type,
                            vat_amount : item.vat_amount,
                            vat_discount_type : item.discount_type,
                        })
                    })
                    if (order.advance_amount) {
                        vm.paymentMethods.length = 0
                        vm.paymentMethods.push({
                            amount: order.advance_amount,
                            method: order.paid_by
                        })
                    }
                    vm.customerNumber = order.customer_number
                    vm.changeToNav('home')
                    vm.getCustomerInfo()
                },
                baseprice: function (item) {
                    let vatType = item.vat_type;
                    let vatAmount = item.vat_amount;
                    let price = item.price;
                    let discountedPriceItemWise = this.discountFromGrandTotal;

                    if (discountedPriceItemWise > 0) {
                        price = price - discountedPriceItemWise;
                    }

                    if (vatType == 'including') {
                        basePrice = price / (1 + vatAmount / 100) ;

                        if (item.vat_discount_type == 'with_vat'){
                            basePrice = (price - item.discountAmount ) / (1 + vatAmount / 100) ;
                        }else if(item.vat_discount_type == 'without_vat'){
                            basePrice = basePrice - item.discountAmount ;
                        }
                        vat = (basePrice * vatAmount) / 100;
                    } else if (vatType == 'excluding') {
                        basePrice = price;
                        if (item.vat_discount_type == 'with_vat'){
                            vat = (basePrice * vatAmount) / 100;
                            basePrice = (basePrice + vat) - item.discountAmount ;
                        } else if (item.vat_discount_type == 'without_vat'){
                            basePrice = basePrice - item.discountAmount ;
                        }
                        vat = (basePrice * vatAmount) / 100;
                    } else {
                        basePrice = price;
                        vat = 0;
                    }

                    return {
                        basePrice: basePrice,
                        vat: vat,
                    };
                },
                // checkPointInput(){
                //     if (this.)
                // }
                // updated() {
                //     $('.bSelect').selectpicker('refresh');
                // }
            }
        });
    })
    ;
</script>
</body>
</html>
