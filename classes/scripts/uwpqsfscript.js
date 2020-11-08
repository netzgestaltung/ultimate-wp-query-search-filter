jQuery(document).ready(function($) {
  var formdata_filter = function(data){
		    var	filter = ['e-psa', 'pruefungsbeispiele', 'module'],
		        getdata = data,
		        index,
				    index_secondary = data.length;
				    
        for ( index = 0; index < data.length; index++ ) {
          if ( data[index].name === 'taxo[0][term]' ) {				    
            if ( $.inArray(data[index].value, filter) ) {
              getdata = data.filter(function(param){
                if ( param.name === 'taxo[1][term][]' ) {
                  return false;
                }
                return true;
              });
            }
          }
        }
        return getdata;
      };
      
	$('body').on('click','.usearchbtn', function(e) {
		process_data($(this));
		return false;
	});

	$('body').on('click','.upagievent', function(e) {
		var pagenumber =  $(this).attr('id');
		var formid = $('#curuform').val();
		upagi_ajax(pagenumber, formid);
		return false;
	});

	$('body').on('keypress','.uwpqsftext',function(e) {
	  if ( e.keyCode == 13 ) {
      e.preventDefault();
      var form = $(this).parent().parent().attr('id');
      if ( !form ) {
        id = $(this);
      } else {
        var id = $('#'+form);
      }
      process_data(id);
	  }
	});

	window.process_data = function ($obj) {
		var ajxdiv = $obj.closest("form").find("#uajaxdiv").val(),
		    res = {loader:$('<div />',{'class':'umloading'}),
				container : $(''+ajxdiv+'')},
				formid = $obj.parent().parent().attr('id'),
				data = formdata_filter($obj.closest("form").serializeArray()),
				class_base = 'uwpqsf-selected-category-',
				pagenum = '1';
						  
    document.body.classList.forEach(function(className){
      if ( className.startsWith(class_base) ) {
        document.body.classList.remove(className);
      }
    });
    
    for ( index = 0; index < data.length; index++ ) {
      if ( data[index].name === 'taxo[0][term]' ) {
        document.body.classList.add(class_base + data[index].value);
      }
    }
    
		jQuery.ajax({
		  type: 'POST',
		  url: ajax.url,
		  data: ({action : 'uwpqsf_ajax',getdata:$.param(data), pagenum:pagenum }),
		  beforeSend:function() {$(''+ajxdiv+'').empty();res.container.append(res.loader);$obj.closest("form").find('input, textarea, button, select').attr("disabled", true);},
		  success: function(html) {
		    res.container.find(res.loader).remove();
			  $(''+ajxdiv+'').html(html);
			  $obj.closest("form").find('input, textarea, button, select').attr("disabled", false);
			}
    });
	}

	window.upagi_ajax = function (pagenum, formid) {
		var ajxdiv = $(''+formid+'').find("#uajaxdiv").val(),
		    res = {loader:$('<div />',{'class':'umloading'}),container : $(''+ajxdiv+'')},
		    data = formdata_filter($(''+formid+'').serializeArray()),
				class_base = 'uwpqsf-selected-category-';

    jQuery.ajax({
      type: 'POST',
      url: ajax.url,
      data: ({action : 'uwpqsf_ajax',getdata:$.param(data), pagenum:pagenum }),
      beforeSend:function() {$(''+ajxdiv+'').empty(); res.container.append(res.loader);},
      success: function(html) {
        res.container.find(res.loader).remove();
        $(''+ajxdiv+'').html(html);
        //res.container.find(res.loader).remove();
      }
    });
	}

  $('body').on('click', '.chktaxoall, .chkcmfall',function () {
    $(this).closest('.togglecheck').find('input:checkbox').prop('checked', this.checked);
  });
});//end of script
