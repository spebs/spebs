$().ready(function() {
	// validate the comment form when it is submitted
	//$("#commentForm").validate();
	
	// validate signup form on keyup and submit
	$("#signupForm").validate({
		rules: {
			//firstname: "required",
			//lastname: "required",
			password: {
				required: {depends: function(){ return $('#id1').hasClass('myvisibleclass')}},
				minlength: 5
			},
			password_confirm: {
				required:  {depends: function(){ return $('#id1').hasClass('myvisibleclass')}},
				minlength: 5,
				equalTo:   "#password"
			},
			email: {
				required: true,
				email:    true
			},
			agree:        "required",

			street:       "required",
			street_num: {
				required: true
			},
			postal_code: {
				required: true,
				minlength: 5,
				maxlength: 5,
				digits:   true
			},
			municipality: "required",
			addrlat: "required",
			addrlng: "required",
			isp: "required",
			bandwidth: "required"
		},
		groups: {
				addrmaplabel: "addrlat addrlng"
				},
		errorPlacement: function(error, element) {
				if (element.attr("name") == "addrlat" || element.attr("name") == "addrlng" )
					$("#errcont").append(error);
				else if (element.attr("name") == "agree")
					$("#agree + span").append(error);
				else
					error.insertAfter(element);
			},
		messages: {
			firstname: "Please enter your firstname",
			lastname: "Please enter your lastname",
			password: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			password_confirm: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long",
				equalTo: "Please enter the same password as above"
			},
			email: "Please enter a valid email address",

			street: "Please enter your street name",
			street_num: "Please enter your street number",
			postal_code: "Please enter your postal code",
			municipality: "Please enter your municipality",
			addrlat: "Please enter a valid address <br/> &nbsp;&nbsp;&nbsp;&nbsp; showing on map",
			addrlng: "Please enter a valid address <br/> &nbsp;&nbsp;&nbsp;&nbsp; showing on map",
			isp: "Please enter your ISP",
			bandwidth: "Please enter your bandwidth",

			agree: "Please accept our policy"
		}
	});
	
});

