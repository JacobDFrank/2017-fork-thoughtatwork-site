$(document).ready(function(){
	$.ajaxSetup({cache: false});
	var speakers;
	var keynotes;

	$.getJSON( "../assets/json/data.json", function(data) {
		speakers = data.speakers;
		keynotes = data.keynoteSpeakers;
		initSecond();
	});

	var mobileQuery = window.matchMedia('(max-width : 767px)');
	var modalShown = false;

	$( document ).ajaxError(function() {
		$('.top-buffer').prepend("Need to be on server to access external speakers file. Here is some test content.");
		console.log("Need to be on server to access external speakers file. Here is some test content.");
		offline();
	});


	function offline(){
		var info = "Bushra\|\|Senior Director\|Nicholas Cage\'presentations\'stuff\|aldksflkj description\|Adding Cage\-ism\|Friday October 21, 7:00\-8:00PM\|loremipsum.com\|http://loremipsum.com\|link.com\|socialMedia.com\\Person\|\|Junior Something\|eqwr\'sdf\'jobs\'stuffs\|dfjljjjf desc\|Bloops\|Sunday, 5\-2AM\|bleepbloop.com\|http://website.com\|otherlink.com\|http://url.com\\Kiddo\|\|Manager of Someplace\|laskdfj\'ekcv\'lafkj\|bnc,mznvew descr\|Meeps\|Monday, 4\-0PM\|meepmeep.com\|http://website.com\|otherotherlink.com\|http://url.com\\Fourtho\|\|Manager of Someotherplace\|cxzxcv\'qweg\'lfdhshh\|erasdgardff descr\|Mops\|Monday, 4\-6PM\|meepmop.com\|http://website.com\|otherslink.com\|http://url.com";
		//sortData(info);
		initSecond();
	}

	function initSecond(){
		$('.speakers-card--grid').each(function(i){
			var headshotSrc = "../assets/graphics/speakers/"+speakers[i].firstName+"_"+speakers[i].lastName+".png";
		    headshotSrc = headshotSrc.toLowerCase();

		    headshotSrc = checkMiguel(speakers[i].firstName, headshotSrc);

		    $(this).children('.speakers-card__headshot').children('img').attr('src', headshotSrc);

		    placeBG($(this), 210, 20);
		});

		$('.speakers-card--keynote').each(function(i){
			placeBG($(this), 350, 500);
		});


		$('.modal-closer').click(function(){
			closeModal();
		});

		$('.modal__x').click(function(){
			closeModal();
		});

	    $('.speakers-card--grid').click(function(){
	      showModal(speakers, this);
	    });

	    $('.speakers-card--keynote').click(function(){
	      showModal(keynotes, this);
	    });
	}

  /*$(window).resize(function(){ // also changes clicking on speaker to clicking on view more
		$('.speaker').off();
		$('.speaker-expand').off();
		$('.speaker-image').off();
		if($('.speaker-expand').css('display') == 'none'){
			//mobile = false;
			$('.speaker').click(function(){
				showModal(this);
			});
		}
		else{
			//mobile = true;
			$('.speaker-expand').click(function(){
				showModal($(this).parents('.speaker'));
			});
			$('.speaker-image').click(function(){
				showModal($(this).parent());
			});
		}
		if(mobileQuery.matches)
			$('.container-fluid').removeClass('blur');
		if(!mobileQuery.matches && modalShown)
			$('.container-fluid').addClass('blur');
	});*/

	function placeBG(speaker, w, h){
	    var backingImg = $(speaker).find('.speakers-card__backing-img').children('img');

	    $(backingImg).css('left', Math.random()*(-1)*(parseInt(backingImg.css('width'))-w));
	    $(backingImg).css('top', Math.random()*(-1)*(parseInt(backingImg.css('height'))-h));
	}

	function checkMiguel(name, path){
		if(name == "Miguel A.")
			return "../assets/graphics/speakers/miguel_cardona.png";
		else
			return path;

	}
  function showModal(speakArray, speaker) {
    modalShown = true;
    //$('.modal__content').detach();
    fillModal(speakArray, $(speaker).index()-1);
    $('.speakers-modal').toggleClass('modal--hidden');
    $('.modal-closer').toggleClass('modal--hidden');

		if (window.location.href.indexOf("speakers") > -1) {
				$(document.body).addClass('noScroll');
		}
		$('.modal-holder').removeClass('noScroll');

    /*if(!mobileQuery.matches)
			$('.container-fluid').addClass('blur');
		$('.speakers-splash-words').attr('id', 'changeBG');*/
  }

  function closeModal() {
    modalShown = false;
    $('.speakers-modal').toggleClass('modal--hidden');
    $('.modal-closer').toggleClass('modal--hidden');

		if (window.location.href.indexOf("speakers") > -1) {
				$(document.body).removeClass('noScroll');
		}
		$('.modal-holder').addClass('noScroll');

	$('.speakers-modal').scrollTop(0);
	$('.modal-holder').scrollTop(0);

    /*if(!mobileQuery.matches)
			$('.container-fluid').removeClass('blur');
		$('.speakers-splash-words').removeAttr('id');*/
  }

  function fillModal(speakArray, i) {
    $('.speakers-modal__name').html(speakArray[i].firstName + " " + speakArray[i].lastName);
    $('.speakers-modal__position').html(speakArray[i].position + " at " + speakArray[i].company);
    $('.speakers-modal__desc').html(speakArray[i].bio);


	var headshotSrc = "../assets/graphics/speakers/"+speakArray[i].firstName+"_"+speakArray[i].lastName+".png";
    headshotSrc = headshotSrc.toLowerCase();
    headshotSrc = checkMiguel(speakArray[i].firstName, headshotSrc);

    $('.speakers-modal__headshot').children('img').attr('src', headshotSrc);
    $('.speakers-modal__social').html("");
    speakArray[i].socialMedia.forEach(function(social) {
    	if(social.type)
      		$('.speakers-modal__social').append('<a href="' + social.url + '"><img src="../assets/graphics/' + social.type + '_teal.svg" class="speakers-modal__social__img"></a>');
      	else
      		$('.speakers-modal__social').append('<a href="' + social.url + '"><img src="../assets/graphics/website_teal.svg" class="speakers-modal__social__img"></a>');
    });

    $('.modal__accord').html('<h5 class="card__header">Presentations</h5>');
    speakArray[i].presentations.forEach(function(pres) {
      $('.modal__accord').append('<p class="modal__accord-title speakers-modal__event"><span>+</span> ' + pres.title + '</p><div class="modal__accord-item"><p>' + pres.desc + '</p></div>');
    });

    $('.modal__accord-title').click(function() {
      if ($(this).children('span').html() == '–')
        $(this).children('span').html('+');
      else
        $(this).children('span').html('&ndash;');

      $(this).next().toggleClass('open');
    });

    /*if($('.speakers-modal__headshot').attr('src') == 'https://taw.imgix.net/speakers/jeff_smith.jpg?w=.779&h=1&crop=focalpoint&fit=crop&fp-x=0.2&fp-y=0.3')
			$('.speakers-modal__headshot').attr('title', 'Jeff didn\'t give us a good picture.');
		else
			$('.speakers-modal__headshot').removeAttr('title');*/
  }
});
