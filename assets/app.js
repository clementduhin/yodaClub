/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.scss";

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log("Hello Webpack Encore! Edit me in assets/app.js");

$(".slider").children().closest(".news").first().addClass("visible");

$(".flecheDroiteSlider").click(function () {
  var cardEnCours = $(this).closest("div").find(".visible");
  var cardSuivante = cardEnCours.next();

  if (cardSuivante.length) {
    cardEnCours.removeClass("visible");
    cardSuivante.addClass("visible");
  } else {
    cardEnCours.parent().children().closest(".news").first().addClass("visible");
    cardEnCours.removeClass("visible");
  }
});

$(".flecheGaucheSlider").click(function () {
  var cardEnCours = $(this).closest("div").find(".visible");
  var cardSuivante = cardEnCours.prev();
  console.log(cardSuivante);

  if (cardSuivante.length) {
    cardEnCours.removeClass("visible");
    cardSuivante.addClass("visible");
  } else {
    cardEnCours.parent().children().closest(".news").last().addClass("visible");
    cardEnCours.removeClass("visible");
  }
});
