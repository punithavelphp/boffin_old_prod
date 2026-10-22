define([
    'jquery',
    'mage/utils/wrapper',
    'mage/template',
    'mage/validation',
    'underscore',
    'jquery/ui'
], function ($) {
    'use strict';
    return function () {

        var string = JSON.stringify($eaCitiesJson),
            obj = JSON.parse(string),
            cityInput = $("[name*='city']").val();
if(string == ''){
			string = JSON.stringify(config.CitiesJson);
			 obj = JSON.parse(string);
}
		var  city_id;
        $(document).ready(function (){
            var region_id = $("[name*='region_id']").val();
            var region = [];

            if (region_id) {
                $.each(obj, function (index, value) {
                    if (value.region_id == region_id) {
                        region.push(value.city_name);
                    }
                });
                var city = $("[name*='city']"),
                    selectCity = city.replaceWith("<select class='required-entry select admin__control-select' name='city' id='city'>") + '</select>',
                    htmlSelect = '<option value="">Select City</option>',
                    options;

                $.each(region, function (index, value) {
                    if ( value == cityInput) {
                        options = '<option value="' + value + '" selected>' + value + '</option>';
                    } else {
                        options = '<option value="' + value + '">' + value + '</option>';
                    }

                    htmlSelect += options;
                });

                $('#city').append(htmlSelect);
            }

        });

        $(document).on('change', "[name*='region_id']", function () {

            var region_id = $(this).val(),
                regionName = this.name,
                cityInputName = regionName.replace("region_id", "city"),
                region = [];

            if (region_id) {
                $.each(obj, function (index, value) {
                    if (value.region_id == region_id) {
                        region.push(value.city_name);
                    }
                });
				
				
							    //$('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find('#citysel').remove()
	//$('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find('#city').remove()
	//$('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find('#city').remove();
						
				
			   city_id = $('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find("[name*='" + cityInputName + "']").attr('id');
				alert(city_id);
				var htmlSelectoptions;
				htmlSelectoptions = '';
				var options;
				htmlSelectoptions += '<option>Select City</option>';
				  $.each(region, function (index, value) {
                    options = '<option value="' + value + '">' + value + '</option>';
                    htmlSelectoptions += options;
                });
				
				 $('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find('#sel_'+city_id).remove();
                var city = $('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find("[name*='" + cityInputName + "']");
				var selectCity;
                     $('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find("[id*='" + city_id + "']").after("<select class='required-entry select admin__control-select' id='sel_"+city_id+"' name='"+cityInputName+"' >" + htmlSelectoptions +'</select>');
 
                                   

 
               //  $('#'+city_id).append(htmlSelectoptions);
				
            }
			
			
			 $(document).on('change', "#sel_"+city_id, function () {
 
			 			var cityname =   $(this).val();
 			 
			 $('.customer_form_areas_address_address_customer_address_update_modal_update_customer_address_form_loader').find('#'+city_id).val(cityname);

			 }); 
			
		

        });
    }; 
});
