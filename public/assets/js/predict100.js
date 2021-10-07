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
                alert('It is not a bee image or is a clip art.')
				$("#prediction-list").empty();
				$("#prediction-list").append('p').text(`Try to upload another photo.`);

				
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
	
	model = await tf.loadModel('https://raw.githubusercontent.com/Yuzhen299/test/master/model.json');
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
	for (let i = 0; i < top5.length; i++) {
		   if (top5[i].className == 'healthy' & top5[i].probability >= 0.95){
			console.log(top5[i].className)
			$("#prediction-list").empty();
			$("#prediction-list").append('p').text('Your Bees Seems to be Healthy!!  But, please check it regularly to keep you Bees healthy.');
		   }

		   
		  }
	
//$("#prediction-list").empty();
/*top5.forEach(function (p) {

	$("#prediction-list").append(`<li>${p.className}: ${p.probability.toFixed(6)}</li>`);

	
	});*/
	
	
});

