// Code is based on a YouTube tutorial by deeplizard
// https://www.youtube.com/watch?v=HEQDRWMK6yY



// After the model loads we want to make a prediction on the default image.
// Thus, the user will see predictions when the page is first loaded.

function simulateClick(tabID) {
	
	document.getElementById(tabID).click();
}



var test;


$("#pre-selector").change(function ()  {
    
    const preview = document.querySelector('img');
    
    var file = document.querySelector('input[type=file]').files[0];

    const reader = new FileReader();

    reader.addEventListener("load", function () {
        // convert image file to base64 string
        preview.src = reader.result;
        processImage(preview.src);
		let dataURL = reader.result;
		$("#selected-image").attr("src", dataURL);
		$("#prediction-list").empty();
        $("#error").empty();

    }, false);
    if (file) {
    reader.readAsDataURL(file);
	//setTimeout(simulateClick.bind(null,'predict-button'), 500);
    
    }
    });


    function makeblob(dataURI) {
    // convert base64 to raw binary data held in a string
    // doesn't handle URLEncoded DataURIs
    var byteString = atob(dataURI.split(',')[1]);
        
    // separate out the mime component
    var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]
        
    // write the bytes of the string to an ArrayBuffer
    var ab = new ArrayBuffer(byteString.length);
    var ia = new Uint8Array(ab);
    for (var i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i);
    }
        
    // write the ArrayBuffer to a blob, and you're done
    var bb = new Blob([ab], {type: mimeString});
    return bb;
    }

    function processImage(sourceImageUrl) {
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
        

        var subscriptionKey = "ee5494252a244188b79c612ab3ea7891";
        var endpoint = "https://beenefit.cognitiveservices.azure.com/";
        
        var uriBase = endpoint + "vision/v3.2/analyze";

        // Request parameters.
        var params = {
            "visualFeatures": "Description,ImageType,Objects",
            "details": "",
            "language": "en",
        };

        

        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),

            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/octet-stream");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },

            type: "POST",
            method: 'POST',

            // Request body.
          
            processData: false,
         
            'data': makeblob(sourceImageUrl)
            
        
        })

        .done(function(data) {
            var responsed = JSON.stringify(data, null, 2)
            var stringify = JSON.parse(responsed);
            var Non_clip_art = stringify['imageType']['clipArtType'];
            var bee_image = stringify['description']['captions'][0]['text'].includes("bee");
            if (bee_image & Non_clip_art == 0) {
                console.log('it is a bee image.');
				
	
					// Simulate a click on the predict button
					setTimeout(simulateClick.bind(null,'predict-button'), 500);
				
            }
            else{
				$("#prediction-list").empty();
                $("#error").empty();
                $("#error").attr('style','color:red').text(`⚠️  It is not a bee image or is a clip art. Try to upload another photo.`);

                
                                    
				
            }
			        

            
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })

        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };




let model;
(async function () {
	
	model = await tf.loadModel('https://beenefit.studio/model/model.json');
	$("#selected-image").attr("src", "assets/img/illustrations/passion.png")
	
	
	
	// Hide the model loading spinner
	$('.progress-bar').hide();
	
	
	
	
})();





$("#predict-button").click(async function () {
	
	
	
	let image = $('#selected-image').get(0);
	
	// Pre-process the image
	let tensor = tf.fromPixels(image)
	.resizeNearestNeighbor([224,224])
	.toFloat();
	
	
	let offset = tf.scalar(127.5);
	
	tensor = tensor.sub(offset)
	.div(offset)
	.expandDims();
	
	
	
	
	// Pass the tensor to the model and call predict on it.
	// Predict returns a tensor.
	// data() loads the values of the output tensor and returns
	// a promise of a typed array when the computation is complete.
	// Notice the await and async keywords are used together.
	let predictions = await model.predict(tensor).data();
	console.log(predictions)
	let top5 = Array.from(predictions)
		.map(function (p, i) { // this is Array.map
			return {
				probability: p,
				className: TARGET_CLASSES[i] // we are selecting the value from the obj
			};

		}).slice(0, 6); // adjust the number of output predictions here.
		console.log(top5)
	var final;

    if (top5[0].probability >= 0.9){
        console.log(top5[0].className)
        console.log(top5[0].probability)
        $("#error").empty();
        $("#prediction-list").empty();
        $("#prediction-list").append('p').text('Your Bees Seems to be Healthy!!  But, please check it regularly to keep you Bees healthy.');
       }
    else{
        var largest = top5[1].probability;
        for (let i = 0; i < top5.length; i++) {
        if (top5[i].className != 'healthy' & largest < top5[i].probability ){
            var largest = top5[i].probability;
            var disease = top5[i].className;
        }
       }
        console.log(disease)
        if (disease == 'few varroa, hive beetles' | disease == 'varroa, small hive beetles' ){
            $("#error").empty();
            $("#prediction-list").empty();
            $("#prediction-list").append(`<h5>Your hive may have varroa problems. </h5>`);
            $("#prediction-list").append(`<p>The control method is shown below:</p>`);
            $("#prediction-list").append(`<li>Using mite-resistant bees</li>`);
            $("#prediction-list").append(`<li>Using small cell comb</li>`);
            $("#prediction-list").append(`<li>Brood break</li>`);
            $("#prediction-list").append(`<li>Using mite trapping</li>`);
            $("#prediction-list").append(`<li>Screened bottom board</li>`);
            $("#prediction-list").append(`<li>Sprinkling or applying powdered sugar on bees</li>`);
            $("#prediction-list").append(`<li>Soft Chemicals (Organic acids, essential oils, and hop beta acids)</li>`);
            $("#prediction-list").append(`<li>Hard Chemicals(acaricides/miticides)</li>`);
        }
        if (disease == 'ant problems'){
            $("#error").empty();
            $("#prediction-list").empty();
            $("#prediction-list").append(`<h5>Your hive may have ant problems. </h5>`);
            $("#prediction-list").append(`<p>The control method is shown below:</p>`);
            $("#prediction-list").append(`<li>Keep your hives off the ground</li>`);
            $("#prediction-list").append(`<li>Monitor the legs of your hive stand</li>`);
            $("#prediction-list").append(`<li>Remove bridges</li>`);
            $("#prediction-list").append(`<li>Repair your boxes</li>`);
            $("#prediction-list").append(`<li>Carry a bucket</li>`);
            $("#prediction-list").append(`<li>Build an oil barrier</li>`);
        }
        if (disease == 'hive being robbed'){
            $("#error").empty();
            $("#prediction-list").empty();
            $("#prediction-list").append(`<h5>Your hive may have robbed problems. </h5>`);
            $("#prediction-list").append(`<p>The control method is shown below:</p>`);
            $("#prediction-list").append(`<li>Close Off The Hive</li>`);
            $("#prediction-list").append(`<li>Apply Vicks Vapor Rub Around The Entrance</li>`);
            $("#prediction-list").append(`<li>Open The Other Hives In The Apiary</li>`);
            $("#prediction-list").append(`<li>Wrap A Wet Towel Around The Hive</li>`);
            $("#prediction-list").append(`<li>Move The Hive</li>`);
        }
        if (disease == 'missing queen'){
            $("#error").empty();
            $("#prediction-list").empty();
            $("#prediction-list").append(`<h5>Your hive may have problems with missing queen. </h5>`);
            $("#prediction-list").append(`<p>The control method is shown below:</p>`);
            $("#prediction-list").append(`<li>Give Them Some Open Worker Brood</li>`);
            $("#prediction-list").append(`<li>Give Them A Queen</li>`);
            $("#prediction-list").append(`<li>Combine The Queenless Beehive With A Queenright Nuc</li>`);
            $("#prediction-list").append(`<li>Destroy The Colony</li>`);
        }

    
    
    }
	
	
	
});


