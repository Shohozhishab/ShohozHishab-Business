<script>

  $(function () {

    $('.transaction').DataTable()
    $('.supplier').DataTable()
    $('.loanProvider').DataTable()
    $('.banktrans').DataTable()
    $('.expense').DataTable()
    $('.othersales').DataTable()
    $('.employeeSalary').DataTable()
    $('.vatpay').DataTable()

    var ladgerTable = $('#example1').DataTable({
      "pageLength": 10,
      "order": [ 0, 'asc' ],
      'ordering'    : true,
    })
    ladgerTable.page('last').draw('page');

    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
    })


      // $('#customer').DataTable();
      // $('#supplier').DataTable();
      // $('#account').DataTable();
      // $('#fund').DataTable();
      // $('#expense').DataTable();
      // $('#other').DataTable();
      // $('#employee').DataTable();
      // $('#vat').DataTable();


  })

// This is for go Back Button at the top -- Start --
function goBack() {
  window.history.back();
}
// This is for go Back Button at the top -- End --

</script>

<script type="text/javascript">
  //sticky menu (start) navbar-fixed-top
  $(window).scroll(function() {
    if ($(this).scrollTop() > 25) {
      $('#stickyNav').show();
    }
  });
  //sticky menu (end)


</script>

<script type="text/javascript">
    //select and search option script (start)
    $(function () {
      //Initialize Select2 Elements
      $('.select2').select2();
    });
    //select and search option script (end)

    //These script is for product purchase module (start)
      function addCart(){
        var category = $('[name=category]').val();
        var subCatId = $('[name=sub_category]').val();
        var name = $('[name=name]').val();
        var unit = $('[name=unit]').val();
        var price = $('[name=price]').val();
        var salePrice = $('[name=selling_price]').val();
        var qty = $('[name=qty]').val();

        // var specialChars = "<>@!#$%^&*()_+[]{}?:;|'\"\\,./~`="
        // var check = function(string){
        //       for(i = 0; i < specialChars.length;i++){
        //           if(string.indexOf(specialChars[i]) > -1){
        //               return true
        //           }
        //       }
        //       return false;
        //   }
        //
        // if(check($('[name=name]').val()) == false){
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('Admin/Purchase/addCart') ?>",
                dataType: "text",
                data: {subCatId: subCatId,name:name,price:price,salePrice:salePrice,qty:qty,category:category,unit:unit},
                success: function(msg){
                    // alert(msg);
                    $('#message').html(msg);
                    location.reload();
                }
            });
        // }else{
        //     $('#nameValid').html('Illegal characters');
        // }

          
          
          
      }

      function remove_cart(Id){
        var Id = $(Id).attr("id");

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Purchase/remove_cart') ?>",
            dataType: "text",
            data: {id:Id},
            success: function(msg){
              location.reload();
            }
          });
      }
      function clearCart(){
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('Admin/Purchase/clearCart') ?>",
            dataType: "text",
            success: function(msg){
              location.reload();
            }
          });
      }

      function showSubCategory(id){
        //alert(id);
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Purchase/check_sub_cat') ?>",
          dataType: "text",
          data: {ID: id},

          beforeSend: function(){
           $('#subCat').html('<img src="<?php print base_url(); ?>/assets/images/loading.gif" width="20" alt="loading"/> Progressing...');
          },
          success: function(msg){
            $('#subCat').html(msg);
          }
        });
      }
      //due amount calculete (start)
      function totalDue(){
        var sub ;
        var totalPrice = $('#totalPrice').val();
        var cash = $("#cash").val();
        var bank = $("#bank").val();
        sub = (totalPrice-cash)-bank;
        $("#totaldue").val(sub);
        $("#totalAmountDue").val(sub);
      }
      $(document).on( 'input', '.cash', function(){ totalDue(); } );
      $(document).on( 'input', '.bank', function(){ totalDue(); } );
      //due amount calculete (end)

      // These script is for product purchase module -- End ---
    </script>

<script type="text/javascript">
      // These script is for product purchase Exixting Itame -- Start ---
      function calculateExixPrice() {
          var totalPrice = 0;

          var n = $("input.datatables").length;
          var prod_id = $("input[name^='prod_id']");
          var qnt = $("input[name^='quantity']");
          var pur_price = $("input[name^='purchase_price']");

          for(i=0;i<n;i++)
          {
              if($('input.datatables')[i].checked) {
                  quantity =  qnt[i].value;
                  purchase_price = pur_price[i].value;
                  itemPrice = purchase_price * quantity;
                  totalPrice = totalPrice + itemPrice;
              }
          }
          $("#totalAmount").val(totalPrice);
          $("#totalDueAmount").val(totalPrice);

      }

        $(document).on( 'input', '.datatables', function(){ calculateExixPrice(); } );
        $(document).on( 'input', '.quantity', function(){ calculateExixPrice(); } );
        $(document).on( 'input', '.purchase_price', function(){ calculateExixPrice(); } );

        function duetotal(){
        var sub ;
        var totalAmount = $('#totalAmount').val();
        var cash = $("#cash").val();
        var bank = $("#bank").val();
        sub = (totalAmount-cash)-bank;

        $("#totalDueAmount").val(sub);
      }
      $(document).on( 'input', '.cash', function(){ duetotal(); } );
      $(document).on( 'input', '.bank', function(){ duetotal(); } );
       // These script is for product purchase Exixting Itame -- End ---
    </script>


<script type="text/javascript">
  //purchase check cash or bank balance (start)
  function checkShopsBalance(cash){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Purchase/check_shop_balance') ?>",
      dataType: "text",
      data: {cash: cash},

      beforeSend: function(){
       $('#Balance_valid').css( 'color','#238A09');
       $('#Balance_valid').html('<img src="<?php print base_url(); ?>/uploads/loading.gif" width="20" alt="loading" > Progressing...');
      },
      success: function(msg){
          if (msg == 1){
              $('#Balance_valid').html('<span style="color:green">Balance is ok</span>');
              document.getElementById("createBtn").disabled = false;
          }
          if (msg == 0){
              $('#Balance_valid').html('<span style="color:red">Balance is too low</span>');
              document.getElementById("createBtn").disabled = true;
          }

      }
    });
  }
  ////purchase check bank balance
  function checkBankBalance(amount){
    var bankId = $('#bank_id').val();
    //alert(bankId);

      if (bankId == 0) {
        $('#Bank_valid').html('<span style="color:red">Please Select Bank</span>');
        $("#bank").val('');
      }else{
        $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Purchase/check_bank_balance') ?>",
          dataType: "text",
          data: {balance: amount,bank_id: bankId},

          beforeSend: function(){
           $('#Bank_valid').css( 'color','#238A09');
           $('#Bank_valid').html('<img src="<?php print base_url(); ?>/uploads/loading.gif" width="20" alt="loading"/> Progressing...');
          },
          success: function(msg){
              if (msg == 1){
                  $('#Bank_valid').html('<span style="color:green">Balance is ok</span>');
                  document.getElementById("createBtn").disabled = false;
              }
              if (msg == 0){
                  $('#Bank_valid').html('<span style="color:red">Balance is too low</span>');
                  document.getElementById("createBtn").disabled = true;
              }
          }
        });
      }

  }
  //purchase check cash or bank balance (end)
</script>
<script type="text/javascript">
  //Money receipt customer select option create (start)
  function checkCustomer(id){
    var view ='';
    if (id == 1) {
       view ='<div class="form-group"><label for="varchar">Existing Customer</label><select class="form-control" name="customer_id"><option>Please Select</option><?php echo getAllListInOption('customer_id','customer_id','customer_name','customers'); ?></select></div>';
        $("#data").html(view).show();
    }else{

      view ='<div class="form-group"><label for="varchar">New Customer</label><input type="text" class="form-control" name="name" id="name" placeholder="Name" value="" /></div>';

      $("#data").html(view).show();
    }
  }
   //Money receipt customer select option create (end)
</script>

<script type="text/javascript">

  //transaction bank check (start)
  function checkBank(id){
    if (id == 1) {
      var view ='<div class="form-group "><label for="varchar">Bank</label><select class="form-control input" name="bank_id" required><option value="">Please select</option><?php echo getTwoValueInOption('bank_id','bank_id','name','account_no','bank'); ?></select></div><div class="form-group" id="chaque"><label for="int">Amount </label><input type="number" step=any class="form-control" name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount" required /> </div>';

      $(".databank").html(view).show();
      $("#databankSup").html(view).show();
      $("#databankLc").html(view).show();
      $("#dataexpense").html(view).show();
      $("#databankloPo").html(view).show();
      $("#employee").html(view).show();
      $("#vatpayId").html(view).show();
      $("#vatpayId").html(view).show();
    }

    if (id == 3) {
      var view2 ='<div class="form-group" ><label>Cheque</label><input type="text" class="form-control input" name="chequeNo" id="chequeNo" placeholder="Input Cheque No "></div><div class="form-group" ><label>Cheque Amount</label><input type="number" step=any onchange="cheque()" class="form-control chequeAmount input" oninput="minusValueCheck(this.value,this)" name="chequeAmount" id="chequeAmount" placeholder="Input Cheque Amount " required ><b id="cheque_valid"></b></div>';
      $("#chaque").html(view2).show();
      $("#databankSup").html(view2).show();
      $("#databankloPo").html(view2).show();
    }

    if (id == 2) {
      var view3 ='<div class="form-group" id="chaque"><label for="int">Amount </label><input type="number" step=any class="form-control input" oninput="minusValueCheck(this.value,this)" name="amount" id="amount" placeholder="Amount" required /> </div>';
      $(".databank").html(view3).show();
      $("#databankSup").html(view3).show();
      $("#databankloPo").html(view3).show();
      $("#databankLc").html(view3).show();
      $("#dataexpense").html(view3).show();
      $("#employee").html(view3).show();
      $("#vatpayId").html(view3).show();

    }
  }

  function checkBank2(id){
    if (id == 1) {
       var view ='<div class="form-group"><label for="varchar">Bank </label><select class="form-control" name="bank_id" required><option value="">Please select</option><?php echo getTwoValueInOption('bank_id','bank_id','name','account_no','bank'); ?></select></div><div class="form-group" id="chaque"><label for="int">Amount</label><input type="number" step=any class="form-control" name="amount" oninput="minusValueCheck(this.value,this)" id="amount" placeholder="Amount" required /> </div>';
      $("#databankloPo").html(view).show();
    }

    if (id == 3) {
      var view2 ='<div class="form-group" ><label>Cheque</label><input type="text" class="form-control" name="chequeNo" id="chequeNo" placeholder="Input Cheque No "></div><div class="form-group" ><label>Cheque Amount</label><input type="number" step=any onchange="cheque()" class="form-control chequeAmount" name="chequeAmount" oninput="minusValueCheck(this.value,this)" id="chequeAmount" placeholder="Input Cheque Amount " required ><b id="cheque_valid"></b></div>';
      $("#databankloPo").html(view2).show();
    }

    if (id == 2) {
      var view3 ='<div class="form-group" id="chaque"><label for="int">Amount </label><input type="number" step=any class="form-control" oninput="minusValueCheck(this.value,this)" name="amount" id="amount" placeholder="Amount" required /> </div>';
      $("#databankloPo").html(view3).show();
    }
  }
 //transaction bank check (end)

 //transaction account holder payment type change (start)
 function changepaymenttype(Id)
 {
    if (Id == 2) {
      var view = '<div class="form-group"><label for="payment_type">Payment Type </label><select class="form-control" onchange="checkBank(this.value)" name="payment_type" required><option value="" >Please Select</option><option value="1"  >Bank</option><option value="3"  >Chaque</option><option value="2" selected>Cash</option></select></div>'
        $("#paymentloPo").html(view).show();
        $("#paymentCus").html(view).show();
        $("#paymentsup").html(view).show();
    }else{
      view ='<div class="form-group" ><label for="payment_type">Payment Type </label><select class="form-control" onchange="checkBank(this.value)" name="payment_type" required><option value="" >Please Select</option><option value="1"  >Chaque/Bank</option><option value="2" selected >Cash</option></select></div>';

      $("#paymentloPo").html(view).show();
      $("#paymentCus").html(view).show();
      $("#paymentsup").html(view).show();
    }
 }
 //transaction account holder payment type change (end)
</script>
<script type="text/javascript">
//All ledger view script (start)

  //supplier ledger view (start)
  function supplTransView(Id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Transaction/suppData') ?>",
        data: {suppId: Id},
        success: function(html){
          $("#suppData").html(html).show();
        }
      });
  }
  //supplier ledger view (end)

  //customer ledger view (start)
  function custoTranDet(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/custData') ?>",
      data: {custId: Id},
      success: function(html){
        $("#custData").html(html).show();
      }
    });
  }
  //customer ledger view (end)

  //loan provider ledger view (start)
  function lonProvTransView(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/lonProvData') ?>",
      data: {lonProvId: Id},
      success: function(html){
        $("#lonProvData").html(html).show();
      }
    });
  }
  //loan provider ledger view (end)


  //lc ledger view (start)
  function lcTransView(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('transaction/lcData') ?>",
      data: {lcId: Id},
      success: function(html){
        $("#lcData").html(html).show();
      }
    });
  }
  //lc ledger view (end)


  //bank ledger view (start)
  function bankTransView(Id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/bankData') ?>",
      data: {bankId: Id},
      success: function(html){
        $("#bankData").html(html).show();
      }
    });
  }
  //bank ledger view (end)

  //All ledger view script (end)
</script>

  <script type="text/javascript">
    //salse moduels search product (start)
      function findResult(){
        $('#keyWord').on('keyup',function(){
          var search_text = $('#keyWord').val();
          if(search_text==""){
                $('#result').empty();
            }else{
                $.ajax({
                  type: "POST",
                  url: "<?php echo site_url('Admin/Sales/search_prod') ?>",
                  data: {keyWord: search_text},

                  success: function(html){

                    $("#result").html(html).show();
                  }
                });
            }
          });
      }
      //salse moduels search product (end)

</script>

<script type="text/javascript">
    //salse moduels search product (start)
      function lcSale(){
        $('#keyWord').on('keyup',function(){
          var search_text = $('#keyWord').val();
          if(search_text==""){
                $('#resultpro').empty();
            }else{
                $.ajax({
                  type: "POST",
                  url: "<?php echo site_url('lc_sales/search_prod') ?>",
                  data: {keyWord: search_text},

                  success: function(html){

                    $("#resultpro").html(html).show();
                  }
                });
            }
          });
      }
      //salse moduels search product (end)

</script>

<script type="text/javascript">
    //return moduels search product (start)
      function returnFindResult(){
        $('#keyWord').on('keyup',function(){
          var search_text = $('#keyWord').val();
          if(search_text==""){
                $('#result').empty();
            }else{
                $.ajax({
                  type: "POST",
                  url: "<?php echo site_url('return_sale/search_prod') ?>",
                  data: {keyWord: search_text},

                  success: function(html){

                    $("#result").html(html).show();
                  }
                });
            }
          });
      }
      //return moduels search product (end)

</script>

<!-- customerLedg list -->
<script type="text/javascript">

  //customer ledger search (start)
  function customerLedg(id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger/search_customerLedg') ?>",
        data: {id: id},
        success: function(html){
          $("#customerLedg").html(html).show();
          var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
        }
      });
  }

  function customerLedgPrint(id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger/search_customerLedgPrint') ?>",
        data: {id: id},
        success: function(html){
          $("#ledgPrint").html(html).show();
        }
      });
  }
  //customer ledger search (end)


  //bank ledger search (start)
  function bankLedg(id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_bank/search_bankLedg') ?>",
        data: {id: id},

        success: function(html){

          $("#bankLedg").html(html).show();
          var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
              ladgerTable.page('last').draw('page');
        }
      });
  }

  function bankLedgPrint(id){
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_bank/search_bankLedgPrint') ?>",
        data: {id: id},

        success: function(html){
          $("#ledgPrint").html(html).show();
        }
      });
  }
  //bank ledger search (end)



  //bank daily ledger search(start)
  function bankLedgdaily(id){
      var date = $(".date").val();
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Daily_book/search_bankLedg') ?>",
        data: {id:id, date:date},
        success: function(html){
          $("#bankdaileyLedg").html(html).show();
        }
      });
  }
  //bank daily ledger search(end)



  //loan provider ledger search(start)
  function lonProviLedg(id){
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_loan/search_lonProvLedg') ?>",
        data: {id: id},
        success: function(html){
          $("#lonProvLedg").html(html).show();
          var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
              ladgerTable.page('last').draw('page');
        }
      });
  }

  function lonProviLedgPrint(id){
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Ledger_loan/search_lonProvLedgPrint') ?>",
        data: {id: id},
        success: function(html){
          $("#ledgPrint").html(html).show();
        }
      });
  }
  //loan provider ledger search(end)



  //suppliers ledger search(start)
  function supplierLedgher(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Ledger_suppliers/search_supplierLedg') ?>",
      data: {id: id},
      success: function(html){
        $("#supplierLedg").html(html).show();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
      }
    });
  }


  function supplierLedgherPrint(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Ledger_suppliers/search_supplierLedgPrint') ?>",
      data: {id: id},
      success: function(html){
        $("#ledgPrint").html(html).show();
      }
    });
  }
   //suppliers ledger search(end)

   // Lc ledger(start)
   function lcLedgview(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_lcLedg') ?>",
      data: {id: id},
      success: function(html){
        $("#lcLedg").html(html).show();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
      }
    });
  }
   // Lc ledger(end)

  function lcLedgPrintview(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_lcLedgPrint') ?>",
      data: {id: id},
      success: function(html){

        $("#lcLedgprint").html(html).show();
      }
    });
  }

   function lcLedSerc(){

    var lc_id = $('#lc_id').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (lc_id == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('ledger_lc/search_datetodate') ?>",
          data: {lcId:lc_id,st_date:st_date,en_date:en_date},

          success: function(val){

            $("#lcLedg").html(val).show();

            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');

          }
        });
    }
  }

   function lcLedSercPrint(){

    var lc_id = $('#lc_id').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('ledger_lc/search_datetodatePrint') ?>",
          data: {lcId:lc_id,st_date:st_date,en_date:en_date},

          success: function(val){
            $("#lcLedgprint").html(html).show();
          }
        });
  }

</script>

<script type="text/javascript">
  //all script sales from calculation needed (start)

      function validationDiscount(id){
          var input =  $('#'+id);
          if (input.val() == '' ){
              input.val(0);
          }else{
              input.val(Number(input.val()));
          }
      }

      //single product sale discount calculet(start)
      function prodcalculate() {
          var itemPrice = 0;
          var dicprice2 = 0;
          var n = $("input[name^='disc']").length;
          var dis = $("input[name^='disc']");
          var subto = $("input[name^='subtotal']");
          if ($("input[name^='disc']").val()) {
              for (i = 0; i < n; i++) {
                  discount = dis[i].value;
                  subtotal = subto[i].value;
                  dicprice = subtotal - ((discount * subtotal) / 100);
                  dicprice2 += (discount * subtotal) / 100;
                  itemPrice += dicprice;
                  $("#subtl_" + i).html(dicprice);
                  $("#subtl2_" + i).val(dicprice);
              }
              $("#totalamount").val(itemPrice);
              $("#grandtotaldue").val(itemPrice.toFixed(2));
              $("#grandtotal").val(itemPrice.toFixed(2));
              $("#grandtotal2").val(itemPrice.toFixed(2));
              $("#grandtotallast").val(itemPrice.toFixed(2));
              $("#granddiscountlast").val(dicprice2.toFixed(2));
          }
        }
      $(document).on( 'input', '.disc', function(){ calculateTotalDiscount(); } );
      //single product sale discount calculet(end)



      //Entire Sale Discount calculet (start)
      function allsalecalculate() {
          var total = 0;

          if ($("#saleDisc").val()) {
              total = (+$("#saleDisc").val() * +$("#totalamount").val()) / 100;
              totalprice = +$("#totalamount").val() - total;
              // yourFloatVarHere.toFixed(2)
              $("#grandtotal").val(totalprice.toFixed(2));
              $("#grandtotaldue").val(totalprice.toFixed(2));
              $("#grandtotallast").val(totalprice.toFixed(2));
              $("#vatTotallast").val(totalprice.toFixed(2));
              $("#saleDiscshow").val(total.toFixed(2));
          }else{
              totalprice = +$("#totalamount").val() + total;
              $("#grandtotal").val(totalprice.toFixed(2));
              $("#grandtotaldue").val(totalprice.toFixed(2));
              $("#grandtotallast").val(totalprice.toFixed(2));
              $("#vatTotallast").val(totalprice.toFixed(2));
              $("#saleDiscshow").val(total.toFixed(2));
          }
      }
      $(document).on( 'input', '.saleDisc', function(){ calculateTotalDiscount(); } );
      //Entire Sale Discount calculet (end)

      //Entire Sale Vat calculet (start)
      function allSaleVatCalculate() {
          var total = 0;
          if ($("#vat").val()) {
              total = (+$("#vat").val() * +$("#vatTotallast").val()) / 100;
              grandtotalaftervat = +$("#vatTotallast").val() + total;
              $("#grandtotal").val(grandtotalaftervat.toFixed(2));
              $("#grandtotaldue").val(grandtotalaftervat.toFixed(2));
              $("#grandtotallast").val(grandtotalaftervat.toFixed(2));
              $("#vatAmount").val(total.toFixed(2));
          }else{
              grandtotalaftervat = +$("#vatTotallast").val() - total;
              $("#grandtotal").val(grandtotalaftervat.toFixed(2));
              $("#grandtotaldue").val(grandtotalaftervat.toFixed(2));
              $("#grandtotallast").val(grandtotalaftervat.toFixed(2));
              $("#vatAmount").val(total.toFixed(2));
          }
        }
      $(document).on( 'input', '.vat', function(){ calculateTotalDiscount(); } );
      //Entire Sale Vat calculet (end)

      function calculateTotalDiscount(){
        prodcalculate();
        allsalecalculate();
        allSaleVatCalculate();
      }

      //salse price new input calculet (start)
      function priceUpCalculate() {
        var total = 0;
        var n = $("input[name^='price']").length;
        var qty = $("input[name^='qty']");
        var upPrice = $("input[name^='price']");
        for(i=0; i<n; i++)
          {
            qt =  qty[i].value;
            up = upPrice[i].value;
            itemPrice = qt * up;
            total += itemPrice;
            $("#subtl_"+i).html(itemPrice);
            $("#subt_"+i).val(itemPrice);
            $("#subtl2_"+i).val(itemPrice);
          }
          $("#totalamount").val(total.toFixed(2));
          $("#grandtotaldue").val(total.toFixed(2));
          $("#grandtotal").val(total.toFixed(2));
          $("#grandtotal2").val(total.toFixed(2));
          $("#grandtotallast").val(total.toFixed(2));
        }
      $(document).on( 'input', '.upprice', function(){ priceUpCalculate(); } );
      //salse price new input calculet (end)


        function checkDueAmount(){
            var duetotal = $("#grandtotaldue").val();
            var name = $("#name").val();
            var customerId = $("#cus").val();

            //if (duetotal >= 0 && name != "" || duetotal >= 0 && customerId != "") {
            if (duetotal < 0 || name == "" && duetotal < 0 || customerId == "") {
                $('#btn').hide();
                $('#mess').html('<span style="color:red">wrong input!! please correct inputs to proceed.</span>');
            }else{
                $('#btn').show();
                $('#mess').html('');
            }
        }



      //sale pay amount calculet (start)
      function totalPay(){
        var totalamount = 0;
        var total = $("#grandtotal").val();
        var nagod = $("#nagod").val();
        var bankAmount = $("#bankAmount").val();
        var chequeAmount = $("#chequeAmount").val();
        totalamount = ((total - nagod) - bankAmount) - chequeAmount;

        $("#grandtotaldue").val(totalamount.toFixed(2));

          checkDueAmount();
      }
      $(document).on( 'input', '.nagod', function(){ totalPay(); } );
      $(document).on( 'input', '.bankAmount', function(){ totalPay(); } );
      $(document).on( 'input', '.chequeAmount', function(){ totalPay(); } );

      //sale pay amount calculet (end)

  function customerBalanceShow(id){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Sales/customerBalance') ?>",
          data: {customer_id:id},
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(data){
              $("#loading-image").hide();
              $('#balance').html('Balance: ৳ '+ data +'/-');
          }
      });
  }

    //all script sales from calculation needed (end)
</script>
<script type="text/javascript">

    //bank select check (start)
      function checkBankId(){
        var bankId = $('#bank_id').val();

          if (bankId == 0) {
            $('#Bank_valid').html('<span style="color:red">Please Select Bank</span>');
            $("#bankAmount").val('');
          }else{
            $('#Bank_valid').html('<span style="color:#238A09">Bank Selected </span>');
          }
      }
      //bank select check (end)


      //cheque number input check (start)
      function cheque(){
        var chequeNo = $('#chequeNo').val();
          if (chequeNo == 0) {
            $('#cheque_valid').html('<span style="color:red">Please input Cheque no..</span>');
            $("#chequeAmount").val('');
          }else{
            $('#cheque_valid').html('<span style="color:#238A09">Cheque no inputed </span>');
          }
      }
      //cheque number input check (start)

</script>
<script type="text/javascript">
  //all checkboxes check (start)
  function toggle(source) {
    checkboxes = document.getElementsByName('productId[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
    }
  }
  //all checkboxes check (end)



//salse create page from valedation (Start)
$('#btn').hide();
var error = 0;
var customer_id = $('[name=customer_id]').val();
var name = $('[name=name]').val();

function createBtnShow(){
    checkDueAmount();
}

// function hideExtraInfo(){
//   var name = $('[name=name]').val();
//   var due = $('#grandtotaldue').val();
//   // if (name) {
//   //   $('[name=chequeNo]').attr("disabled", "disabled");
//   //   $('[name=chequeAmount]').attr("disabled", "disabled");
//   //   if (due > 0 ) {
//   //       error = 1;
//   //       $('#btn').hide();
//   //       $('#mess').html('<span style="color:red">Please clear due.</span>');
//   //   }else{
//   //     $('#btn').show();
//   //     $('#mess').html('');
//   //   }
//     checkDueAmount();
//   }


$(document).on( 'input', '#nagod', function(){ calculateDueAndShowBtn(); } );
$(document).on( 'input', '#bankAmount', function(){ calculateDueAndShowBtn(); } );
$(document).on( 'input', '#name', function(){ calculateDueAndShowBtn(); } );
function calculateDueAndShowBtn() {
    var totalPay = 0;
    var nagod = $('[name=nagod]').val();
    var bankAmount = $('[name=bankAmount]').val();
    totalPay = (+nagod) + (+bankAmount);
    var grandtotal = $('[name=grandtotal]').val();
    var name = $('[name=name]').val();
    if (name) {
      if (totalPay == grandtotal) {
        $('#btn').show();
        $('#mess').html('');
      }else{
        error = 1;
        $('#btn').hide();
        $('#mess').html('<span style="color:red">Please Input correct your price.</span>');
      }

    }
}
//salse create page from valedation (End)

</script>

<script type="text/javascript">
  //employee  All Query (start)
  function employeeSearch(Id)
  {
      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Transaction/search_employeeSalary') ?>",
        data: {id: Id},

        success: function(val){

          $("#salary").val(val).show();
        }
      });

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('transaction/ledger_employee') ?>",
        data: {id: Id},

        success: function(val){

          $("#ledger_employee").html(val).show();
        }
      });
  }

  function employeeLedg(Id)
  {
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_employee/search_ledger_employee') ?>",
          data: {id: Id},

          success: function(val){
            $("#ledger_employee").html(val).show();

            var name = $("#employeeId option:selected").text()

              $("#prName").html(name);
          }
        });
  }
  //employee  All Query (end)
</script>


<script type="text/javascript">
  //customer ledgher date to date search (start)
  function cusLedSerc()
  {
    var customerId = $('#customerId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (customerId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger/search_dateTodate') ?>",
          data: {customerId:customerId,st_date:st_date,en_date:en_date},

          success: function(val){

            $("#customerLedg").html(val).show();
            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
              ladgerTable.page('last').draw('page');

          }
        });
    }
  }

  function cusLedSercPrint()
  {
    var customerId = $('#customerId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger/search_dateTodatePrint') ?>",
          data: {customerId:customerId,st_date:st_date,en_date:en_date},

          success: function(val){
            $("#ledgPrint").html(val).show();
          }
        });
  }
  //customer ledgher date to date search (end)
</script>

<script type="text/javascript">
  //supplier ledgher date to date search (start)
  function suppLedSerc()
  {
    var supplierId = $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (supplierId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_suppliers/search_datetodate') ?>",
          data: {supplierId:supplierId,st_date:st_date,en_date:en_date},

          success: function(val){

            $("#supplierLedg").html(val).show();

            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');

          }
        });
    }
  }

  function suppLedSercprint(){
    var supplierId = $('#supplierId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (supplierId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_suppliers/search_datetodatePrint') ?>",
          data: {supplierId:supplierId,st_date:st_date,en_date:en_date},

          success: function(val){
            $("#ledgPrint").html(val).show();
          }
        });
    }
  }
  //supplier ledgher date to date search (end)
</script>

<script type="text/javascript">
  //bank ledgher date to date search (start)
  function bankLedSerc()
  {
    var bankId = $('#bankId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (bankId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_bank/search_dateTodate') ?>",
          data: {bankId:bankId,st_date:st_date,en_date:en_date},

          success: function(val){

            $("#bankLedg").html(val).show();
            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');
          }
        });
    }
  }

  function bankLedSercPrint()
  {
    var bankId = $('#bankId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_bank/search_dateTodatePrint') ?>",
          data: {bankId:bankId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
          $("#loading-image").hide();
            $("#ledgPrint").html(val).show();
          }
        });
  }
  //bank ledgher date to date search (end)
</script>

<script type="text/javascript">
  //loan Provider ledgher date to date search (start)
  function loProLedSerc()
  {
    var loanProId = $('#loanProId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (loanProId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_loan/search_dateTodate') ?>",
          data: {loanProId:loanProId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
            $("#loading-image").hide();
            $("#lonProvLedg").html(val).show();
            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');

          }
        });
    }
  }

  function loProLedSercprint()
  {
    var loanProId = $('#loanProId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();


      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_loan/search_dateTodatePrint') ?>",
          data: {loanProId:loanProId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
            $("#loading-image").hide();
            $("#ledgPrint").html(val).show();

          }
        });
  }
  //loan Provider ledgher date to date search (end)
</script>

<script type="text/javascript">
  //employee ledgher date to date search (start)
  function emplLedSerc()
  {
    var employeeId = $('#employeeId').val();
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('span.select2-selection').css("border-color", "white");
    $('span.select2-selection').css("background-color", "#ccc");
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");
    $('#prStD').html(st_date);
    $('#prEtD').html(en_date);


    if (employeeId == '') {
      $('span.select2-selection').css("border-color", "red");
      $('span.select2-selection').css("background-color", "#ff9393");
      error = 1;
    }
    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_employee/search_dateTodate') ?>",
          data: {employeeId:employeeId,st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
            $("#loading-image").hide();
            $("#ledger_employee").html(val).show();
          }
        });
    }
  }

  function showHeader(){
      $('#printHead').css("display", "block");

  }
  //employee ledgher date to date search (end)
</script>

<script type="text/javascript">
  //Nagod ledgher date to date search (start)
  function nagodLedSerc()
  {
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();
    var error = 0;
    $('#st_date').css("border-color", "white");
    $('#st_date').css("background-color", "#ccc");
    $('#en_date').css("border-color", "white");
    $('#en_date').css("background-color", "#ccc");


    if (st_date == '') {
      $('#st_date').css("border-color", "red");
      $('#st_date').css("background-color", "#ff9393");
      error = 1;
    }
    if (en_date == '') {
      $('#en_date').css("border-color", "red");
      $('#en_date').css("background-color", "#ff9393");
      error = 1;
    }

    if (error == 0){

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_nagodan/search') ?>",
          data: {st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
            $("#loading-image").hide();
            $("#ledger_cash").html(val).show();
            var ladgerTable = $('#example1').DataTable({
                "pageLength": 10,
                "order": [ 0, 'asc' ],
                'ordering'    : true,
              })
            ladgerTable.page('last').draw('page');
          }
        });
    }
  }


  function nagodLedSercPrint()
  {
    var st_date = $('#st_date').val();
    var en_date = $('#en_date').val();


      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Ledger_nagodan/searchPrint') ?>",
          data: {st_date:st_date,en_date:en_date},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(val){
          $("#loading-image").hide();
            $("#ledger_cashPrint").html(val).show();
            $("#printbutt").css('display','block');
          }
        });
    }
  //Nagod ledgher date to date search (end)
</script>

<script type="text/javascript">
  function storePro(storeId)
  {
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Stock_report/search_store_product') ?>",
      data: {storeId:storeId},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(val){
        $("#loading-image").hide();
        $("#product").html(val).show();
      }
    });
  }
</script>


<script type="text/javascript">

  function vatLedgerView(vatId)
  {
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('Admin/Transaction/vatLedgerData') ?>",
      data: {vatId:vatId},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(val){
        $("#loading-image").hide();
        $("#vatledger").html(val).show();
      }
    });
  }



  function checkAvailableBankAmount(amount)
  {
    var bankId = $('#bank_id').val();

      $.ajax({
        type: "POST",
        url: "<?php echo site_url('Admin/Transaction/check_bank_balance') ?>",
        dataType: "text",
        data: {balance: amount,bank_id: bankId},

        beforeSend: function() {
          $("#loading-image").show();
        },
        success: function(msg){
          $("#loading-image").hide();
            $('#Bank_valid').html(msg);

        }
      });
  }

</script>


<script type="text/javascript">
  //return sale form validetion (start)
  function hideExtraInfoReturn(){
    var name = $('[name=name]').val();
    var customerId = $('[name=customer_id]').val();
    var due = $('#grandtotaldue').val();
    //alert(customerId);
    if (name || customerId) {
      $('[name=chequeNo]').attr("disabled", "disabled");
      $('[name=chequeAmount]').attr("disabled", "disabled");
      if (due > 0 ) {
          error = 1;
          $('#btn').hide();
          $('#mess').html('<span style="color:red">Please Input correct your price.</span>');
      }else{
        $('#btn').show();
        $('#mess').html('');
      }
    }
  }

  $(document).on( 'input', '.nagodpay', function(){ calculateDueAndReturnSaleShowBtn(); } );
  $(document).on( 'input', '.bankAmountpay', function(){ calculateDueAndReturnSaleShowBtn(); } );

  function calculateDueAndReturnSaleShowBtn() {
    var totalPay = 0;
    var nagod = $('[name=nagod]').val();
    var bankAmount = $('[name=bankAmount]').val();
    totalPay = (+nagod) + (+bankAmount);
    var grandtotal = $('[name=grandtotal]').val();
    var name = $('[name=name]').val();
    var customerId = $('[name=customer_id]').val();
    if (name || customerId) {
      if (totalPay >= grandtotal) {
        $('#btn').show();
        $('#mess').html('');
      }else{
        error = 1;
        $('#btn').hide();
        $('#mess').html('<span style="color:red">Please Input correct your price.</span>');
      }
    }
  }

  //return sale form validetion (end)





  // These script is for product purchase Exixting Itame -- Start ---
        function check_pro_qty() {

          var n = $("input.datatables").length;

          var newqnt = $("input[name^='quantity']");
          var oldqnt = $("input[name^='qty']");

          var oldquantity = 0;
          var newquantity = 0;
          for(i=0;i<n;i++)
          {
            if($('input.datatables')[i].checked) {
                  oldquantity =  oldqnt[i].value;
                  newquantity =  newqnt[i].value;

                  if (oldquantity >= newquantity ) {

                    $("#quantity").css("border-color", "#ecf0f5");
                  }else{

                    $("#quantity").css("border-color", "#f90808");
                  }
            }
          }

        }

        $(document).on( 'input', '.datatables', function(){ check_pro_qty(); } );
        $(document).on( 'input', '.quantity', function(){ check_pro_qty(); } );

</script>

<script type="text/javascript">
  function getCustomer(id){
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('lc_transaction/lcCustomer') ?>",
        dataType: "text",
        data: {Id:id},

      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(msg){
        $("#loading-image").hide();
          $('#lccustomer').html(msg);
      }
    });
  }


  function getSupplier(id){
    $.ajax({
        type: "POST",
        url: "<?php echo site_url('lc_transaction/lcSupplier') ?>",
        dataType: "text",
        data: {Id:id},

      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(msg){
        $("#loading-image").hide();
          $('#supplierId').html(msg);
      }
    });
  }

  function lcLedgDebit(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_Debit') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedg").html(html).show();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
          // $("#print").css('display','none');
      }
    });
  }

  function lcLedgDebitPrint(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_Debitprint') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedgprint").html(html).show();
          // $("#print").css('display','none');
      }
    });
  }

  function lcLedgCredit(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_Credit') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedg").html(html).show();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
          })
          ladgerTable.page('last').draw('page');
      }
    });
  }

  function lcLedgCreditPrint(id){
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('ledger_lc/search_CreditPrint') ?>",
      data: {id: id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
      $("#loading-image").hide();
        $("#lcLedgprint").html(html).show();
      }
    });
  }



</script>

<!-- print -->
<script type="text/javascript">

  function printDiv(divName) {
       var printContents = document.getElementById(divName).innerHTML;
       document.body.innerHTML = printContents;
       window.print();
       location.reload();
  }

  function showData(url,getUrl2){

    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $("#viewpage").html(html).show();
        $("#viewpage").css("margin-left","0px");
        $('.select2').select2();
        var ladgerTable = $('#example1').DataTable({
            "pageLength": 10,
            "order": [ 0, 'asc' ],
            'ordering'    : true,
        });
        ladgerTable.page('last').draw('page');

          $('#customer2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#supplier2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#account2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#fund2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#expense2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#other2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#employee2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });
          $('#vat2').DataTable({
              "pageLength": 10,
              "order": [ 0, 'asc' ],
              'ordering'    : true,
          });

        yearpicker();
        history.pushState({ foo: 'bar' }, '', '<?php echo base_url() ?>'+getUrl2);
      }
    });
  }

  function activeTab(val){
      $('.treeview-menu li').attr('class', '');
      $('ul .active').attr('class', '');
      $('.treeview .menu-open').attr('class', '');
      $(val).parent().attr('class','active');
  }

  $(document).on('submit','#geniusform',function(e){
    e.preventDefault();



    $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
    var fd = new FormData(this);

    var geniusform = $(this);
    $('button.geniusSubmit-btn').prop('disabled',true);
    $.ajax({
      method:"POST",
      url:$(this).prop('action'),
      data:fd,
      contentType: false,
      cache: false,
      processData: false,
      beforeSend: function() {
        $("#loading-image").show();
      },
      success:function(data){
        $("#loading-image").hide();
          $('#message').hide();
          $('#message').show();
          $('#message').html(data);
          $('#geniusform')[0].reset();
          $.ajax({
            method:"get",
            url:"<?php echo site_url('Admin/Transaction/updated_case') ?>",
            success:function(cash){
                $('#reloaddiv').html(cash);
            }
          });
          $(".input").val("");
          $("#subCat").val("");
          $("#custData").html("");
          $("#suppData").html("");
          $("#lonProvData").html("");
          $("#bankData").html("");
          $("#ledger_employee").html("");
          $("#vatledger").html("");
          $(".viewpage").html(data);
          $('#reload').load(document.URL + ' #reload');
          $('#reloadimg').load(document.URL + ' #reloadimg');
        $('button.geniusSubmit-btn').prop('disabled',false);
        $(window).scrollTop(0);
      }

    });

  });

  function customerTypeValidat(){
      var typeName = $('#type_name').val();

      if (required(typeName) == false){
          $('#type_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Type name field cannot be empty</div>');
      }else if(numericOrStringCheck(typeName) == false){
          $('#type_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(typeName) == false){
          $('#type_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#type_name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var type_name_validation = true;
      }

      if (type_name_validation == true){
          $('#geniusform').submit();
      }

  }

  function customerValidat(){
      var name = $('#customer_name').val();
      var mobile = $('#mobile').val();
      var cus_type = $('#cus_type_id').val();

      if (required(name) == false){
          $('#customer_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer name field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#customer_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#customer_name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#customer_name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var customer_name_validation = true;
      }

      if (required(mobile) == false){
          $('#mobile').parent().find('.error').html('<div style="color:red;" id="mesWrong">Mobile field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#mobile').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#mobile').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(cus_type) == false){
          $('#cus_type_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer Type field cannot be empty</div>');
      }else{
          $('#cus_type_id').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var cus_type_validation = true;
      }

      if ((customer_name_validation == true) && (phone_validation == true) &&(cus_type_validation == true) ){
          $('#geniusform').submit();
          $('#customerform').submit();
      }
  }

  function customerExisValidat(){
      var name = $('#customer_name_ex').val();
      var mobile = $('#mobile_ex').val();
      var cus_type = $('#cus_type_id_ex').val();
      var transaction_type = $('#transaction_type').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#customer_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer name field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#customer_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#customer_name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#customer_name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var customer_name_validation = true;
      }

      if (required(mobile) == false){
          $('#mobile_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Mobile field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#mobile_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#mobile_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(cus_type) == false){
          $('#cus_type_id_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Customer Type field cannot be empty</div>');
      }else{
          $('#cus_type_id_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var cus_type_validation = true;
      }

      if (required(transaction_type) == false){
          $('#transaction_type').parent().find('.error').html('<div style="color:red;" id="mesWrong">Transaction Type field cannot be empty</div>');
      }else{
          $('#transaction_type').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var transaction_type_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Amount Type field cannot be empty</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((customer_name_validation == true) && (phone_validation == true) && (cus_type_validation == true) && (transaction_type_validation == true) && (amount_validation == true) ){
          $('#geniusform3').submit();
          $('#customerform2').submit();
      }
  }

  function bankValidat(){
      var name = $('#name').val();
      var account_no = $('#account_no').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(account_no) == false){
          $('#account_no').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(account_no) == false){
          $('#account_no').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else if (lengthValidation(account_no) == false){
          $('#account_no').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#account_no').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var account_no_validation = true;
      }

      if ((name_validation == true) && (account_no_validation == true)){
          $('#geniusform').submit();
      }
  }

  function bankExValidat(){
      var name = $('#name_ex').val();
      var account_no = $('#account_no_ex').val();
      var amount_ex = $('#amount_ex').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(account_no) == false){
          $('#account_no_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(account_no) == false){
          $('#account_no_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else if (lengthValidation(account_no) == false){
          $('#account_no_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#account_no_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var account_no_validation = true;
      }

      if (required(amount_ex) == false){
          $('#amount_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount_ex) == false){
          $('#amount_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          // $('#amount_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_ex_validation = true;
      }


      if ((name_validation == true) && (account_no_validation == true) && (amount_ex_validation == true)){
          $('#geniusform3').submit();
      }
  }

  function bankDepositValidat() {
      var bank = $('#bank_id').val();
      var amount = $('#amount').val();

      if (required(bank) == false){
          $('#bank_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          // $('#bank_id').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var bank_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          // $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }


      if ((bank_validation == true) && (amount_validation == true)){
          $('#geniusform').submit();
      }
  }

  function brandValidat() {
      var name = $('#name').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }


      if (name_validation == true){
          $('#geniusform').submit();
      }
  }

  function proCatValidat() {
      var name = $('#product_category').val();

      if (required(name) == false){
          $('#product_category').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#product_category').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#product_category').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#product_category').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }


      if (name_validation == true){
          $('#geniusform').submit();
      }
  }

  function storeValidat() {
      var name = $('#name').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }


      if (name_validation == true){
          $('#geniusform').submit();
      }
  }

  function suppliersValidat(){
      var name = $('#name').val();
      var mobile = $('#phone').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          // $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          // $('#phone').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true)){
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">success</div>');
          $('#phone').parent().find('.error').html('<div style="color:green;" id="mesWrong">success</div>');
          $('#geniusform').submit();
          $('#supplierform').submit();
      }
  }

  function suppliersExValidat(){
      var name = $('#name_ex').val();
      var mobile = $('#phone_ex').val();
      var transaction_type = $('#transaction_type').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phone_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(transaction_type) == false){
          $('#transaction_type').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#transaction_type').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var transaction_type_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true) && (transaction_type_validation == true) && (amount_validation == true)){
          $('#geniusform3').submit();
          $('#supplierform2').submit();
      }
  }

  function accountValidat() {
      var name = $('#name').val();
      var mobile = $('#phone').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phone').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true)){
          $('#geniusform').submit();
      }
  }

  function accountExValidat(){
      var name = $('#name_ex').val();
      var mobile = $('#phone_ex').val();
      var transaction_type = $('#transaction_type').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phone_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phone_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if (required(transaction_type) == false){
          $('#transaction_type').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#transaction_type').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var transaction_type_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true) && (transaction_type_validation == true) && (amount_validation == true)){
          $('#geniusform3').submit();
      }
  }

  function employeeValidat() {
      var name = $('#name').val();
      var salary = $('#salary').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(salary) == false){
          $('#salary').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(salary) == false){
          $('#salary').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#salary').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var salary_validation = true;
      }

      if ((name_validation == true) && (salary_validation == true)){
          $('#geniusform').submit();
      }
  }

  function employeeExValidat() {
      var name = $('#name_ex').val();
      var salary = $('#salary_ex').val();
      var amount = $('#amount').val();

      if (required(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(salary) == false){
          $('#salary_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(salary) == false){
          $('#salary_ex').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#salary_ex').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var salary_validation = true;
      }

      if (required(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(amount) == false){
          $('#amount').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#amount').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var amount_validation = true;
      }

      if ((name_validation == true) && (salary_validation == true) && (amount_validation == true)){
          $('#geniusform3').submit();
      }
  }

  function userValidat(){
      var name = $('#name').val();
      var email = $('#email').val();
      var password = $('#password').val();
      var con_password = $('#con_password').val();
      var role_id = $('#role_id').val();
      var status = $('#status').val();

      if (required(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#name').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#name').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(password) == false){
          $('#password').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#password').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var password_validation = true;
      }

      if (required(con_password) == false){
          $('#con_password').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#con_password').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var con_password_validation = true;
      }

      if (required(role_id) == false){
          $('#role_id').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#role_id').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var role_id_validation = true;
      }

      if (required(status) == false){
          $('#status').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else{
          $('#status').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var status_validation = true;
      }

      if (required(email) == false){
          $('#email').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(emailValidation(email) == false){
          $('#email').parent().find('.error').html('<div style="color:red;" id="mesWrong">Enter a valid email address</div>');
      }else{
          $('#email').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var email_validation = true;
      }

      if ((name_validation == true) && (password_validation == true) && (con_password_validation == true) && (role_id_validation == true) && (status_validation == true) && (email_validation == true) ){
          $('#geniusform').submit();
      }


  }




  function numericOrStringCheck(val){
      if ($.isNumeric(val) == false) {
          return true;
      }else {
          return false;
      };
  }
  function required(val){
      if (val == ""){
          return false;
      }else{
          return true;
      }
  }
  function lengthValidation(val){
      var length = val.length;
      if (length <= 32){
          return true;
      }else{
          return false;
      }
  }
  function notNumericCheck(val){
      if ($.isNumeric(val) == true) {
          return true;
      }else {
          return false;
      };
  }

  function emailValidation(val){
      var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
      // return emailReg.test(val);
      if (!emailReg.test(val)) {
          return false;
      }else {
          return true;
      };
  }

  function not0(val){
      if (val == 0){
          return false;
      }else{
          return true;
      }
  }
  function emojiCheck(val){
      // if (val == 0){
      //     return false;
      // }else{
      //     return true;
      // }
  }



  $(document).on('submit','#geniusform3',function(e){
      e.preventDefault();

      $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
      var fd = new FormData(this);

      var geniusform = $(this);
      $('button.geniusSubmit-btn3').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#message').hide();
              $('#message').show();
              $('#message').html(data);
              $('#geniusform3')[0].reset();
              $.ajax({
                  method:"get",
                  url:"<?php echo site_url('transaction/updated_case') ?>",
                  success:function(cash){
                      $('#reloaddiv').html(cash);
                  }
              });
              $(".input").val("");
              $("#custData").html("");
              $("#suppData").html("");
              $("#lonProvData").html("");
              $("#bankData").html("");
              $("#ledger_employee").html("");
              $("#vatledger").html("");
              $(".viewpage").html(data);
              $('#reload').load(document.URL + ' #reload');
              $('#reloadimg').load(document.URL + ' #reloadimg');
              $('button.geniusSubmit-btn3').prop('disabled',false);
              $(window).scrollTop(0);
          }

      });

  });

  $(document).on('submit','#geniusformUpdate',function(e){
      e.preventDefault();

      $('#message').html("<div class='alert alert-secondary'>Loading..... please wait</div>");
      var fd = new FormData(this);

      var geniusform = $(this);
      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#message').hide();
              $('#message').show();
              $('#message').html(data);
              $.ajax({
                  method:"get",
                  url:"<?php echo site_url('Admin/Transaction/updated_case') ?>",
                  success:function(cash){
                      $('#reloaddiv').html(cash);
                  }
              });
              $(".input").val("");
              $("#custData").html("");
              $("#suppData").html("");
              $("#lonProvData").html("");
              $("#bankData").html("");
              $("#ledger_employee").html("");
              $("#vatledger").html("");
              $(".viewpage").html(data);
              $('#reload').load(document.URL + ' #reload');
              $('#reloadimg').load(document.URL + ' #reloadimg');
              $('button.geniusSubmit-btn').prop('disabled',false);
              $(window).scrollTop(0);
          }

      });

  });


  function _delete(url,getUrl){
      var confirmed = confirm('Are You Sure ?');
      if (confirmed) {
          $.ajax({
              type: "GET",
              url: url,
              data: {},
              beforeSend: function () {
                  $("#loading-image").show();
              },
              success: function (mess) {
                  $("#loading-image").hide();
                  $('#message').html(mess);
                  $('#reload').load(document.URL + '#reload table');
              }
          });
      }
  }

  // starting_closing module (start)

  $(document).on('submit','#geniusform2',function(e){
    e.preventDefault();

    var fd = new FormData(this);
    var geniusform2 = $(this);

    if (confirm("your previous data can be destroyed! Are you sure?")) {
      $('button.geniusSubmit-btn2').prop('disabled',true);
      $.ajax({
        method:"POST",
        url:$(this).prop('action'),
        data:fd,
        contentType: false,
        cache: false,
        processData: false,
        success:function(data){
            $('#message').hide();
            $('#message').show();
            $('#message').html(data);
            $('#geniusform2')[0].reset();
            //$.ajax({
            //  method:"get",
            //  beforeSend: function() {
            //    $("#loading-image").show();
            //  },
            //  url:"<?php //echo site_url('transaction/updated_case') ?>//",
            //  success:function(cash){
            //    $("#loading-image").hide();
            //      $('#reloaddiv').html(cash);
            //  }
            //});

          $('button.geniusSubmit-btn2').prop('disabled',false);
          $(window).scrollTop(0);
        }

      });
    }

  });
   // starting_closing module (end)



// yearly_closing module (start)
  function closingData(url){
     if(confirm("your previous data can be destroyed! Are you sure?")){
        $.ajax({
          type: "POST",
          url: url,
          data: {},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(html){
            $("#loading-image").hide();
            $('#message').html(html);
          }
        });
    }
  }
// yearly_closing module (end)

//shop_opening_status change(start)
function opening_status(url){
     if(confirm("Are you confirming to run the business?")){
        $.ajax({
          type: "POST",
          url: url,
          data: {},
          beforeSend: function() {
            $("#loading-image").show();
          },
          success: function(html){
            $("#loading-image").hide();
            $('#message').html(html);
            setTimeout(function(){
                location.reload();
            }, 1000);
          }
        });
    }
  }
//shop_opening_status change(end)

  // 'previus data show' modules script (start)
  function yearpicker(){
    let startYear = 2021;
    let endYear = new Date().getFullYear();
    for (i = endYear; i > startYear; i--)
    {
      $('#yearpicker').append($('<option></option>').val(i).html(i));
    }
  }

  $(document).ready(
    yearpicker()
  );


  function url_ledger(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#hedden").show();
        $("#loading-image").hide();
        $('#all_user').html(html);
        $('#nameLabel').html('');
        $('.select2').select2();
        // alert(html);
      }
    });
  }

  function searchLedger(id,url){
    $.ajax({
      type: "POST",
      url: url,
      data: {Id:id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $('#nameLabel').html(html);
      }
    });
  }

  function searchLedgercash(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $("#hedden").hide();
        $('#nameLabel').html(html);
      }
    });
  }

  function reportView(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        // $("#hedden").hide();
        $('#reportView').html(html);
      }
    });
  }

  function reportData(url){
    $.ajax({
      type: "POST",
      url: url,
      data: {},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $('#reportView').html(html);
        $('.select2').select2();
      }
    });
  }

  function searchReport(id,url){
    $.ajax({
      type: "POST",
      url: url,
      data: {Id:id},
      beforeSend: function() {
        $("#loading-image").show();
      },
      success: function(html){
        $("#loading-image").hide();
        $('#reportResult').html(html);
      }
    });
  }
  // 'previus data show' modules script (end)

  function QrScan(id){
      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Sales/scanAddToCart') ?>",
          data: {prod_id:id},
          beforeSend: function() {
              $("#loading-image").show();
          },
          success: function(message){
              $("#loading-image").hide();
              $("#message").html(message);
              $("#TFtable").load(location.href + " #TFtable");
              $('#qrKey').val('');

          }
      });
  }



  function minusValueCheck(val,identity){
      if ( val < 0 ){
          $(identity).val('');
          $(identity).parent().append('<div style="color:red;" id="mesWrong">Wrong Input</div>');
      }else{
          $('#mesWrong').css('color','green');
          $('#mesWrong').html('Success');
      }
  }

  function addtocartService(){
      var service_name = $('[name=service_name]').val();
      var price = $('[name=price]').val();

      $.ajax({
          type: "POST",
          url: "<?php echo site_url('Admin/Service/addCart') ?>",
          dataType: "text",
          data: {service_name: service_name,price:price},
          success: function(msg){
              $('#message').html(msg);
              location.reload();
          }
      });
  }

  function generateBtnShow(){
      var duetotal = $("#grandtotaldue").val();
      var serviceType = $("#service_type").val();
      var title = $("#title").val();
      var name = $("#nameId").val();
      var customerId = $("#cus").val();

      var error = 0;
      if (customerId == "" && name == "" ) { error = 1; }
      if (serviceType == "" ) { error = 1; }
      if (title == "" ) { error = 1; }
      if (duetotal < 0 ) { error = 1; }
      if (error == 1 ) {
          $('#btn').hide();
          $('#mess').html('<span style="color:red">wrong input!! please correct inputs to proceed.</span>');
      }else{
          $('#btn').show();
          $('#mess').html('');
      }
  }

  $(document).on('submit','#supplierform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var supplierform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#message2').html(data.message);
              if(data.success == true){
                  $('#supplierform')[0].reset();
                  $('#supData').html(data.htmlData);
                  $('#supplier_id').html(data.htmlData);
                  setTimeout(function() {
                      $('#message2').fadeOut();
                      $('#modal-default').modal('hide');
                  }, 2000);
              }
          }
      });

  });

  $('#modal-customer').on('hidden.bs.modal', function () {
      $('#cusmessage2').empty();
      $('.error').empty();
  });

  $('#modal-default').on('hidden.bs.modal', function () {
      $('#message2').empty();
      $('.error').empty();
  });

  $('#modal-account').on('hidden.bs.modal', function () {
      $('#messageAcc').empty();
      $('.error').empty();
  });

  $(document).on('submit','#customerform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var customerform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#cusmessage2').html(data.message);

              if(data.success == true){
                  $('#customerform')[0].reset();
                  $('#cusData').html(data.htmlData);
                  $('#cus').html(data.htmlData);
                  setTimeout(function() {
                      $('#message').fadeOut();
                      $('#modal-customer').modal('hide');
                  }, 2000);

              }
          }
      });

  });

  function accountHeadValidat() {
      var name = $('#nameAcc').val();
      var mobile = $('#phoneAcc').val();

      if (required(name) == false){
          $('#nameAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(numericOrStringCheck(name) == false){
          $('#nameAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric not allow!</div>');
      }else if (lengthValidation(name) == false){
          $('#nameAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">maximum length of 32 characters</div>');
      }else{
          $('#nameAcc').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var name_validation = true;
      }

      if (required(mobile) == false){
          $('#phoneAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">This field cannot be empty</div>');
      }else if(notNumericCheck(mobile) == false){
          $('#phoneAcc').parent().find('.error').html('<div style="color:red;" id="mesWrong">Only numeric allow!</div>');
      }else{
          $('#phoneAcc').parent().find('.error').html('<div style="color:green;" id="mesWrong">Success</div>');
          var phone_validation = true;
      }

      if ((name_validation == true) && (phone_validation == true)){
          $('#accountform').submit();
      }
  }

  $(document).on('submit','#accountform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var accountform = $(this);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#messageAcc').html(data.message);

              if(data.success == true){
                  $('#accountform')[0].reset();
                  $('#loanData').html(data.htmlData);
                  setTimeout(function() {
                      $('#messageAcc').fadeOut();
                      $('#modal-account').modal('hide');
                  }, 2000);
              }
          }
      });

  });


  $(document).on('submit','#cusUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var cusUpdateform = $(this);
      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#cusUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#customer2').load(document.URL + ' #customer2');

          }

      });

  });

  $(document).on('submit','#suppUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#suppUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#supplier2').load(document.URL + ' #supplier2');
          }

      });

  });

  $(document).on('submit','#accountUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#accountUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#account2').load(document.URL + ' #account2');
          }

      });

  });

  $(document).on('submit','#otherSalesUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#otherSalesUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#other2').load(document.URL + ' #other2');
          }

      });

  });

  $(document).on('submit','#employeeUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#employeeUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#employee2').load(document.URL + ' #employee2');
          }

      });

  });

  $(document).on('submit','#expenseUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#expenseUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#expense2').load(document.URL + ' #expense2');
          }

      });

  });

  $(document).on('submit','#vatUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#vatUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#vat2').load(document.URL + ' #vat2');
          }

      });

  });

  $(document).on('submit','#fundUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#fundUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#fund2').load(document.URL + ' #fund2');
          }

      });

  });

  $(document).on('submit','#depositUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#depositUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#example1').load(document.URL + ' #example1');
          }

      });

  });

  $(document).on('submit','#withdrawUpdateform',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      $('button.geniusSubmit-btn').prop('disabled',true);
      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $('#modal-default').modal('hide');
              $('#messageAcc').hide();
              $('#messageAcc').show();
              $('#messageAcc').html(data);
              $('#withdrawUpdateform')[0].reset();
              $('#reload').load(document.URL + ' #reload');
              $('#example1').load(document.URL + ' #example1');
              $('#bankData').load(document.URL + ' #bankData');
          }

      });

  });

  $(document).on('submit','#customerform2',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var customerform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#cusmessage2').html(data.message);

              if(data.success == true){
                  $('#customerform2')[0].reset();
                  $('#cusData').html(data.htmlData);
                  $('#cus').html(data.htmlData);
                  $('#reload').load(document.URL + ' #reload');
                  setTimeout(function() {
                      $('#message').fadeOut();
                      $('#modal-customer').modal('hide');
                  }, 2000);

              }
          }
      });

  });

  $(document).on('submit','#supplierform2',function(e){
      e.preventDefault();

      var fd = new FormData(this);

      var supplierform = $(this);

      $.ajax({
          method:"POST",
          url:$(this).prop('action'),
          data:fd,
          dataType : 'json',
          contentType: false,
          cache: false,
          processData: false,
          beforeSend: function() {
              $("#loading-image").show();
          },
          success:function(data){
              $("#loading-image").hide();
              $("#message").hide();
              $('#message2').html(data.message);
              if(data.success == true){
                  $('#supplierform2')[0].reset();
                  $('#supData').html(data.htmlData);
                  $('#supplier_id').html(data.htmlData);
                  setTimeout(function() {
                      $('#message2').fadeOut();
                      $('#modal-default').modal('hide');
                  }, 2000);
              }
          }
      });

  });

</script>






