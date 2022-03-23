jQuery(function($)
{
	function set_category_text(dom_obj)
	{
		var dom_obj_campaigns = $("#str-ecom .campaigns"),
			dom_obj_category_text = dom_obj_campaigns.children(".webshop_category_text"),
			category_href = dom_obj.attr('href'),
			category_text = "<p>Testtext... (" + category_href + ")</p>";

		/*console.log(category_href);*/

		dom_obj_campaigns.addClass('loaded');

		if(dom_obj_category_text.length > 0)
		{
			dom_obj_category_text.html(category_text);
		}

		else
		{
			dom_obj_campaigns.append("<div class='webshop_category_text'>" + category_text + "</div>");
		}
	}

	$(document).on('click', "#str-ecom .flickity-slider > .btn", function()
	{
		set_category_text($(this));
	});

	var dom_obj_active = $("#str-ecom .flickity-slider > .btn.active");

	if(dom_obj_active.length > 0)
	{
		/*console.log("Yes");*/

		set_category_text(dom_obj_active);
	}

	else
	{
		/*console.log("Nope");*/

		var webshop_interval = setInterval(function()
		{
			if($("#str-ecom .campaigns").hasClass('loaded'))
			{
				clearInterval(webshop_interval);

				/*console.log("Done (Timeout)");*/
			}

			else
			{
				dom_obj_active = $("#str-ecom .flickity-slider > .btn.active");

				if(dom_obj_active.length > 0)
				{
					/*console.log("Yes (Timeout)");*/

					set_category_text(dom_obj_active);
				}

				else
				{
					/*console.log("Nope (Timeout)");*/

					$("#str-ecom .campaigns").addClass('hide');
				}
			}
		}, 2000);
	}
});