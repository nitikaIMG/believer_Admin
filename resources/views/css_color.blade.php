    <?php
        use App\Helpers\Helpers;
    ?>

  <style>
      @import url({{Helpers::settings()->font_awesome_link ?? 'https://kit-pro.fontawesome.com/releases/v5.14.0/css/pro.min.css'}});
      @import url('{{Helpers::settings()->font_family_link ?? 'https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap'}}');
  </style>


  <style>
    :root {
      --font-family: <?php echo Helpers::settings()->font_family_name ?? "'Rubik', sans-serif" ?>;



        /*  Calculations Based on Lightness  */
        --lightnessTransform: 10%;
        --darknessTransform: 15%;
        --contrastThreshold: 60%;



        /*  primary  */
        --primary-color-h: <?php echo explode(' ', Helpers::settings()->primary_hsl ?? '215')[0] ?? '215'; ?>;
        --primary-color-s: <?php echo explode(' ', Helpers::settings()->primary_hsl ?? '95')[1] ?? '95'; ?>%;
        --primary-color-l: <?php echo explode(' ', Helpers::settings()->primary_hsl ?? '51')[2] ?? '51'; ?>%;


        --primary-color-light-l: calc(var(--primary-color-l) + var(--lightnessTransform));
        --primary-color-dark-l: calc(var(--primary-color-l) - var(--darknessTransform));
        --color-primary: hsl(var(--primary-color-h),var(--primary-color-s),var(--primary-color-l));
        --color-primary-dark: hsl(var(--primary-color-h),var(--primary-color-s),var(--primary-color-dark-l));
        --color-primary-light: hsl(var(--primary-color-h),var(--primary-color-s),var(--primary-color-light-l));


        /*  transparent  */
        --transparent-color-h: <?php echo explode(' ', Helpers::settings()->primary_hsl ?? '215')[0] ?? '215'; ?>;
        --transparent-color-s: <?php echo explode(' ', Helpers::settings()->primary_hsl ?? '95')[1] ?? '95'; ?>%;
        --transparent-color-l: <?php echo explode(' ', Helpers::settings()->primary_hsl ?? '51')[2] ?? '51'; ?>%;


        --transparent-color-light-l: calc(var(--primary-color-l) + var(--lightnessTransform));
        --transparent-color-dark-l: calc(var(--primary-color-l) - var(--darknessTransform));
        --color-transparent: hsl(var(--primary-color-h)var(--primary-color-s)var(--primary-color-l) / 0%);
        --color-transparent-dark: hsl(var(--primary-color-h)var(--primary-color-s)var(--primary-color-dark-l) / 0%);
        --color-transparent-light: hsl(var(--primary-color-h)var(--primary-color-s)var(--primary-color-light-l) / 0%);



        /*  secondary  */
        --secondary-color-h: <?php echo explode(' ', Helpers::settings()->secondary_hsl ?? '273')[0] ?? '273'; ?>;
        --secondary-color-s: <?php echo explode(' ', Helpers::settings()->secondary_hsl ?? '100')[1] ?? '100'; ?>%;
        --secondary-color-l: <?php echo explode(' ', Helpers::settings()->secondary_hsl ?? '38')[2] ?? '38'; ?>%;


        --secondary-color-light-l: calc(var(--secondary-color-l) + var(--lightnessTransform));
        --secondary-color-dark-l: calc(var(--secondary-color-l) - var(--darknessTransform));
        --color-secondary: hsl(var(--secondary-color-h),var(--secondary-color-s),var(--secondary-color-l));
        --color-secondary-dark: hsl(var(--secondary-color-h),var(--secondary-color-s),var(--secondary-color-dark-l));
        --color-secondary-light: hsl(var(--secondary-color-h),var(--secondary-color-s),var(--secondary-color-light-l));



    /*  info  */
    --info-color-h: <?php echo explode(' ', Helpers::settings()->info_hsl ?? '188')[0] ?? '188'; ?>;
    --info-color-s: <?php echo explode(' ', Helpers::settings()->info_hsl ?? '78')[1] ?? '78'; ?>%;
    --info-color-l: <?php echo explode(' ', Helpers::settings()->info_hsl ?? '41')[2] ?? '41'; ?>%;

    --info-color-light-l: calc(var(--info-color-l) + var(--lightnessTransform));
    --info-color-dark-l: calc(var(--info-color-l) - var(--darknessTransform));
    --color-info: hsl(var(--info-color-h),var(--info-color-s),var(--info-color-l));
    --color-info-dark: hsl(var(--info-color-h),var(--info-color-s),var(--info-color-dark-l));
    --color-info-light: hsl(var(--info-color-h),var(--info-color-s),var(--info-color-light-l));



    /*  success  */
    --success-color-h: <?php echo explode(' ', Helpers::settings()->success_hsl ?? '134')[0] ?? '134'; ?>;
    --success-color-s: <?php echo explode(' ', Helpers::settings()->success_hsl ?? '61')[1] ?? '61'; ?>%;
    --success-color-l: <?php echo explode(' ', Helpers::settings()->success_hsl ?? '42')[2] ?? '42'; ?>%;

    --success-color-light-l: calc(var(--success-color-l) + var(--lightnessTransform));
    --success-color-dark-l: calc(var(--success-color-l) - var(--darknessTransform));
    --color-success: hsl(var(--success-color-h),var(--success-color-s),var(--success-color-l));
    --color-success-dark: hsl(var(--success-color-h),var(--success-color-s),var(--success-color-dark-l));
    --color-success-light: hsl(var(--success-color-h),var(--success-color-s),var(--success-color-light-l));



    /*  danger  */
    --danger-color-h: <?php echo explode(' ', Helpers::settings()->danger_hsl ?? '354')[0] ?? '354'; ?>;
    --danger-color-s: <?php echo explode(' ', Helpers::settings()->danger_hsl ?? '70')[1] ?? '70'; ?>%;
    --danger-color-l: <?php echo explode(' ', Helpers::settings()->danger_hsl ?? '53')[2] ?? '53'; ?>%;

    --danger-color-light-l: calc(var(--danger-color-l) + var(--lightnessTransform));
    --danger-color-dark-l: calc(var(--danger-color-l) - var(--darknessTransform));
    --color-danger: hsl(var(--danger-color-h),var(--danger-color-s),var(--danger-color-l));
    --color-danger-dark: hsl(var(--danger-color-h),var(--danger-color-s),var(--danger-color-dark-l));
    --color-danger-light: hsl(var(--danger-color-h),var(--danger-color-s),var(--danger-color-light-l));



    /*  warning  */
    --warning-color-h: <?php echo explode(' ', Helpers::settings()->warning_hsl ?? '45')[0] ?? '45'; ?>;
    --warning-color-s: <?php echo explode(' ', Helpers::settings()->warning_hsl ?? '100')[1] ?? '100'; ?>%;
    --warning-color-l: <?php echo explode(' ', Helpers::settings()->warning_hsl ?? '51')[2] ?? '51'; ?>%;

    --warning-color-light-l: calc(var(--warning-color-l) + var(--lightnessTransform));
    --warning-color-dark-l: calc(var(--warning-color-l) - var(--darknessTransform));
    --color-warning: hsl(var(--warning-color-h),var(--warning-color-s),var(--warning-color-l));
    --color-warning-dark: hsl(var(--warning-color-h),var(--warning-color-s),var(--warning-color-dark-l));
    --color-warning-light: hsl(var(--warning-color-h),var(--warning-color-s),var(--warning-color-light-l));



    /*  light  */
    --light-color-h: <?php echo explode(' ', Helpers::settings()->light_hsl ?? '210')[0] ?? '210'; ?>;
    --light-color-s: <?php echo explode(' ', Helpers::settings()->light_hsl ?? '16')[1] ?? '16'; ?>%;
    --light-color-l: <?php echo explode(' ', Helpers::settings()->light_hsl ?? '97')[2] ?? '97'; ?>%;

    --light-color-light-l: calc(var(--light-color-l) + var(--lightnessTransform));
    --light-color-dark-l: calc(var(--light-color-l) - var(--darknessTransform));
    --color-light: hsl(var(--light-color-h),var(--light-color-s),var(--light-color-l));
    --color-light-dark: hsl(var(--light-color-h),var(--light-color-s),var(--light-color-dark-l));
    --color-light-light: hsl(var(--light-color-h),var(--light-color-s),var(--light-color-light-l));



    /*  dark  */
    --dark-color-h: <?php echo explode(' ', Helpers::settings()->dark_hsl ?? '210')[0] ?? '210'; ?>;
    --dark-color-s: <?php echo explode(' ', Helpers::settings()->dark_hsl ?? '10')[1] ?? '10'; ?>%;
    --dark-color-l: <?php echo explode(' ', Helpers::settings()->dark_hsl ?? '23')[2] ?? '23'; ?>%;

    --dark-color-light-l: calc(var(--dark-color-l) + var(--lightnessTransform));
    --dark-color-dark-l: calc(var(--dark-color-l) - var(--darknessTransform));
    --color-dark: hsl(var(--dark-color-h),var(--dark-color-s),var(--dark-color-l));
    --color-dark-dark: hsl(var(--dark-color-h),var(--dark-color-s),var(--dark-color-dark-l));
    --color-dark-light: hsl(var(--dark-color-h),var(--dark-color-s),var(--dark-color-light-l));



    /*  black  */
    --black-color-h: <?php echo explode(' ', Helpers::settings()->black_hsl ?? '0')[0] ?? '0'; ?>;
    --black-color-s: <?php echo explode(' ', Helpers::settings()->black_hsl ?? '0')[1] ?? '0'; ?>%;
    --black-color-l: <?php echo explode(' ', Helpers::settings()->black_hsl ?? '0')[2] ?? '0'; ?>%;

    --black-color-light-l: calc(var(--black-color-l) + var(--lightnessTransform));
    --black-color-dark-l: calc(var(--black-color-l) - var(--darknessTransform));
    --color-black: hsl(var(--black-color-h),var(--black-color-s),var(--black-color-l));
    --color-black-dark: hsl(var(--black-color-h),var(--black-color-s),var(--black-color-dark-l));
    --color-black-light: hsl(var(--black-color-h),var(--black-color-s),var(--black-color-light-l));



    /*  white  */
    --white-color-h: <?php echo explode(' ', Helpers::settings()->white_hsl ?? '0')[0] ?? '0'; ?>;
    --white-color-s: <?php echo explode(' ', Helpers::settings()->white_hsl ?? '0')[1] ?? '0'; ?>%;
    --white-color-l: <?php echo explode(' ', Helpers::settings()->white_hsl ?? '100')[2] ?? '100'; ?>%;

    --white-color-light-l: calc(var(--white-color-l) + var(--lightnessTransform));
    --white-color-dark-l: calc(var(--white-color-l) - var(--darknessTransform));
    --color-white: hsl(var(--white-color-h),var(--white-color-s),var(--white-color-l));
    --color-white-dark: hsl(var(--white-color-h),var(--white-color-s),var(--white-color-dark-l));
    --color-white-light: hsl(var(--white-color-h),var(--white-color-s),var(--white-color-light-l));



    /*  muted  */
    --muted-color-h: <?php echo explode(' ', Helpers::settings()->muted_hsl ?? '0')[0] ?? '0'; ?>;
    --muted-color-s: <?php echo explode(' ', Helpers::settings()->muted_hsl ?? '0')[1] ?? '0'; ?>%;
    --muted-color-l: <?php echo explode(' ', Helpers::settings()->muted_hsl ?? '87')[2] ?? '87'; ?>%;

    --muted-color-light-l: calc(var(--muted-color-l) + var(--lightnessTransform));
    --muted-color-dark-l: calc(var(--muted-color-l) - var(--darknessTransform));
    --color-muted: hsl(var(--muted-color-h),var(--muted-color-s),var(--muted-color-l));
    --color-muted-dark: hsl(var(--muted-color-h),var(--muted-color-s),var(--muted-color-dark-l));
    --color-muted-light: hsl(var(--muted-color-h),var(--muted-color-s),var(--muted-color-light-l));



    /*  blue  */
    --blue-color-h: <?php echo explode(' ', Helpers::settings()->blue_hsl ?? '210')[0] ?? '210'; ?>;
    --blue-color-s: <?php echo explode(' ', Helpers::settings()->blue_hsl ?? '100')[1] ?? '100'; ?>%;
    --blue-color-l: <?php echo explode(' ', Helpers::settings()->blue_hsl ?? '50')[2] ?? '50'; ?>%;

    --blue-color-light-l: calc(var(--blue-color-l) + var(--lightnessTransform));
    --blue-color-dark-l: calc(var(--blue-color-l) - var(--darknessTransform));
    --color-blue: hsl(var(--blue-color-h),var(--blue-color-s),var(--blue-color-l));
    --color-blue-dark: hsl(var(--blue-color-h),var(--blue-color-s),var(--blue-color-dark-l));
    --color-blue-light: hsl(var(--blue-color-h),var(--blue-color-s),var(--blue-color-light-l));



    /*  indigo  */
    --indigo-color-h: <?php echo explode(' ', Helpers::settings()->indigo_hsl ?? '263')[0] ?? '263'; ?>;
    --indigo-color-s: <?php echo explode(' ', Helpers::settings()->indigo_hsl ?? '90')[1] ?? '90'; ?>%;
    --indigo-color-l: <?php echo explode(' ', Helpers::settings()->indigo_hsl ?? '50')[2] ?? '50'; ?>%;

    --indigo-color-light-l: calc(var(--indigo-color-l) + var(--lightnessTransform));
    --indigo-color-dark-l: calc(var(--indigo-color-l) - var(--darknessTransform));
    --color-indigo: hsl(var(--indigo-color-h),var(--indigo-color-s),var(--indigo-color-l));
    --color-indigo-dark: hsl(var(--indigo-color-h),var(--indigo-color-s),var(--indigo-color-dark-l));
    --color-indigo-light: hsl(var(--indigo-color-h),var(--indigo-color-s),var(--indigo-color-light-l));



    /*  purple  */
    --purple-color-h: <?php echo explode(' ', Helpers::settings()->purple_hsl ?? '268')[0] ?? '268'; ?>;
    --purple-color-s: <?php echo explode(' ', Helpers::settings()->purple_hsl ?? '68')[1] ?? '68'; ?>%;
    --purple-color-l: <?php echo explode(' ', Helpers::settings()->purple_hsl ?? '45')[2] ?? '45'; ?>%;

    --purple-color-light-l: calc(var(--purple-color-l) + var(--lightnessTransform));
    --purple-color-dark-l: calc(var(--purple-color-l) - var(--darknessTransform));
    --color-purple: hsl(var(--purple-color-h),var(--purple-color-s),var(--purple-color-l));
    --color-purple-dark: hsl(var(--purple-color-h),var(--purple-color-s),var(--purple-color-dark-l));
    --color-purple-light: hsl(var(--purple-color-h),var(--purple-color-s),var(--purple-color-light-l));



    /*  pink  */
    --pink-color-h: <?php echo explode(' ', Helpers::settings()->pink_hsl ?? '332')[0] ?? '332'; ?>;
    --pink-color-s: <?php echo explode(' ', Helpers::settings()->pink_hsl ?? '79')[1] ?? '79'; ?>%;
    --pink-color-l: <?php echo explode(' ', Helpers::settings()->pink_hsl ?? '57')[2] ?? '57'; ?>%;

    --pink-color-light-l: calc(var(--pink-color-l) + var(--lightnessTransform));
    --pink-color-dark-l: calc(var(--pink-color-l) - var(--darknessTransform));
    --color-pink: hsl(var(--pink-color-h),var(--pink-color-s),var(--pink-color-l));
    --color-pink-dark: hsl(var(--pink-color-h),var(--pink-color-s),var(--pink-color-dark-l));
    --color-pink-light: hsl(var(--pink-color-h),var(--pink-color-s),var(--pink-color-light-l));



    /*  red  */
    --red-color-h: <?php echo explode(' ', Helpers::settings()->red_hsl ?? '354')[0] ?? '354'; ?>;
    --red-color-s: <?php echo explode(' ', Helpers::settings()->red_hsl ?? '70')[1] ?? '70'; ?>%;
    --red-color-l: <?php echo explode(' ', Helpers::settings()->red_hsl ?? '53')[2] ?? '53'; ?>%;

    --red-color-light-l: calc(var(--red-color-l) + var(--lightnessTransform));
    --red-color-dark-l: calc(var(--red-color-l) - var(--darknessTransform));
    --color-red: hsl(var(--red-color-h),var(--red-color-s),var(--red-color-l));
    --color-red-dark: hsl(var(--red-color-h),var(--red-color-s),var(--red-color-dark-l));
    --color-red-light: hsl(var(--red-color-h),var(--red-color-s),var(--red-color-light-l));



    /*  orange  */
    --orange-color-h: <?php echo explode(' ', Helpers::settings()->orange_hsl ?? '27')[0] ?? '27'; ?>;
    --orange-color-s: <?php echo explode(' ', Helpers::settings()->orange_hsl ?? '98')[1] ?? '98'; ?>%;
    --orange-color-l: <?php echo explode(' ', Helpers::settings()->orange_hsl ?? '53')[2] ?? '53'; ?>%;

    --orange-color-light-l: calc(var(--orange-color-l) + var(--lightnessTransform));
    --orange-color-dark-l: calc(var(--orange-color-l) - var(--darknessTransform));
    --color-orange: hsl(var(--orange-color-h),var(--orange-color-s),var(--orange-color-l));
    --color-orange-dark: hsl(var(--orange-color-h),var(--orange-color-s),var(--orange-color-dark-l));
    --color-orange-light: hsl(var(--orange-color-h),var(--orange-color-s),var(--orange-color-light-l));



    /*  yellow  */
    --yellow-color-h: <?php echo explode(' ', Helpers::settings()->yellow_hsl ?? '45')[0] ?? '45'; ?>;
    --yellow-color-s: <?php echo explode(' ', Helpers::settings()->yellow_hsl ?? '100')[1] ?? '100'; ?>%;
    --yellow-color-l: <?php echo explode(' ', Helpers::settings()->yellow_hsl ?? '51')[2] ?? '51'; ?>%;

    --yellow-color-light-l: calc(var(--yellow-color-l) + var(--lightnessTransform));
    --yellow-color-dark-l: calc(var(--yellow-color-l) - var(--darknessTransform));
    --color-yellow: hsl(var(--yellow-color-h),var(--yellow-color-s),var(--yellow-color-l));
    --color-yellow-dark: hsl(var(--yellow-color-h),var(--yellow-color-s),var(--yellow-color-dark-l));
    --color-yellow-light: hsl(var(--yellow-color-h),var(--yellow-color-s),var(--yellow-color-light-l));



    /*  green  */
    --green-color-h: <?php echo explode(' ', Helpers::settings()->green_hsl ?? '134')[0] ?? '134'; ?>;
    --green-color-s: <?php echo explode(' ', Helpers::settings()->green_hsl ?? '61')[1] ?? '61'; ?>%;
    --green-color-l: <?php echo explode(' ', Helpers::settings()->green_hsl ?? '40')[2] ?? '40'; ?>%;

    --green-color-light-l: calc(var(--green-color-l) + var(--lightnessTransform));
    --green-color-dark-l: calc(var(--green-color-l) - var(--darknessTransform));
    --color-green: hsl(var(--green-color-h),var(--green-color-s),var(--green-color-l));
    --color-green-dark: hsl(var(--green-color-h),var(--green-color-s),var(--green-color-dark-l));
    --color-green-light: hsl(var(--green-color-h),var(--green-color-s),var(--green-color-light-l));



    /*  teal  */
    --teal-color-h: <?php echo explode(' ', Helpers::settings()->teal_hsl ?? '162')[0] ?? '162'; ?>;
    --teal-color-s: <?php echo explode(' ', Helpers::settings()->teal_hsl ?? '72')[1] ?? '72'; ?>%;
    --teal-color-l: <?php echo explode(' ', Helpers::settings()->teal_hsl ?? '46')[2] ?? '46'; ?>%;

    --teal-color-light-l: calc(var(--teal-color-l) + var(--lightnessTransform));
    --teal-color-dark-l: calc(var(--teal-color-l) - var(--darknessTransform));
    --color-teal: hsl(var(--teal-color-h),var(--teal-color-s),var(--teal-color-l));
    --color-teal-dark: hsl(var(--teal-color-h),var(--teal-color-s),var(--teal-color-dark-l));
    --color-teal-light: hsl(var(--teal-color-h),var(--teal-color-s),var(--teal-color-light-l));



    /*  cyan  */
    --cyan-color-h: <?php echo explode(' ', Helpers::settings()->cyan_hsl ?? '188')[0] ?? '188'; ?>;
    --cyan-color-s: <?php echo explode(' ', Helpers::settings()->cyan_hsl ?? '78')[1] ?? '78'; ?>%;
    --cyan-color-l: <?php echo explode(' ', Helpers::settings()->cyan_hsl ?? '40')[2] ?? '40'; ?>%;

    --cyan-color-light-l: calc(var(--cyan-color-l) + var(--lightnessTransform));
    --cyan-color-dark-l: calc(var(--cyan-color-l) - var(--darknessTransform));
    --color-cyan: hsl(var(--cyan-color-h),var(--cyan-color-s),var(--cyan-color-l));
    --color-cyan-dark: hsl(var(--cyan-color-h),var(--cyan-color-s),var(--cyan-color-dark-l));
    --color-cyan-light: hsl(var(--cyan-color-h),var(--cyan-color-s),var(--cyan-color-light-l));



    /*  gray  */
    --gray-color-h: <?php echo explode(' ', Helpers::settings()->gray_hsl ?? '208')[0] ?? '208'; ?>;
    --gray-color-s: <?php echo explode(' ', Helpers::settings()->gray_hsl ?? '7')[1] ?? '7'; ?>%;
    --gray-color-l: <?php echo explode(' ', Helpers::settings()->gray_hsl ?? '46')[2] ?? '46'; ?>%;

    --gray-color-light-l: calc(var(--gray-color-l) + var(--lightnessTransform));
    --gray-color-dark-l: calc(var(--gray-color-l) - var(--darknessTransform));
    --color-gray: hsl(var(--gray-color-h),var(--gray-color-s),var(--gray-color-l));
    --color-gray-dark: hsl(var(--gray-color-h),var(--gray-color-s),var(--gray-color-dark-l));
    --color-gray-light: hsl(var(--gray-color-h),var(--gray-color-s),var(--gray-color-light-l));



    /*  graydark  */
    --graydark-color-h: <?php echo explode(' ', Helpers::settings()->graydark_hsl ?? '213')[0] ?? '213'; ?>;
    --graydark-color-s: <?php echo explode(' ', Helpers::settings()->graydark_hsl ?? '10')[1] ?? '10'; ?>%;
    --graydark-color-l: <?php echo explode(' ', Helpers::settings()->graydark_hsl ?? '17')[2] ?? '17'; ?>%;

    --graydark-color-light-l: calc(var(--graydark-color-l) + var(--lightnessTransform));
    --graydark-color-dark-l: calc(var(--graydark-color-l) - var(--darknessTransform));
    --color-graydark: hsl(var(--graydark-color-h),var(--graydark-color-s),var(--graydark-color-l));
    --color-graydark-dark: hsl(var(--graydark-color-h),var(--graydark-color-s),var(--graydark-color-dark-l));
    --color-graydark-light: hsl(var(--graydark-color-h),var(--graydark-color-s),var(--graydark-color-light-l));

    }





    /*  Apply Color to Boxes */


    .text-n-primary {
      --switch: calc((var(--primary-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-primary-light {
      --switch: calc((var(--primary-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-primary-dark {
      --switch: calc((var(--primary-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }


    .text-n-secondary {
      --switch: calc((var(--secondary-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-secondary-light {
      --switch: calc((var(--secondary-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-secondary-dark {
      --switch: calc((var(--secondary-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-success {
      --switch: calc((var(--success-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-success-light {
      --switch: calc((var(--success-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-success-dark {
      --switch: calc((var(--success-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-danger {
      --switch: calc((var(--danger-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-danger-light {
      --switch: calc((var(--danger-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-danger-dark {
      --switch: calc((var(--danger-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-warning {
      --switch: calc((var(--warning-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-warning-light {
      --switch: calc((var(--warning-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-warning-dark {
      --switch: calc((var(--warning-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-info {
      --switch: calc((var(--info-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-info-light {
      --switch: calc((var(--info-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-info-dark {
      --switch: calc((var(--info-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-dark {
      --switch: calc((var(--dark-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-dark-light {
      --switch: calc((var(--dark-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-dark-dark {
      --switch: calc((var(--dark-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-black {
      --switch: calc((var(--black-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-black-light {
      --switch: calc((var(--black-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-black-dark {
      --switch: calc((var(--black-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-light {
      --switch: calc((var(--light-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-light-light {
      --switch: calc((var(--light-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-light-dark {
      --switch: calc((var(--light-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-white {
      --switch: calc((var(--white-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-white-light {
      --switch: calc((var(--white-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-white-dark {
      --switch: calc((var(--white-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-muted {
      --switch: calc((var(--muted-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-muted-light {
      --switch: calc((var(--muted-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-muted-dark {
      --switch: calc((var(--muted-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-blue {
      --switch: calc((var(--blue-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-blue-light {
      --switch: calc((var(--blue-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-blue-dark {
      --switch: calc((var(--blue-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-indigo {
      --switch: calc((var(--indigo-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-indigo-light {
      --switch: calc((var(--indigo-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-indigo-dark {
      --switch: calc((var(--indigo-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-purple {
      --switch: calc((var(--purple-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-purple-light {
      --switch: calc((var(--purple-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-purple-dark {
      --switch: calc((var(--purple-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-pink {
      --switch: calc((var(--pink-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-pink-light {
      --switch: calc((var(--pink-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-pink-dark {
      --switch: calc((var(--pink-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-red {
      --switch: calc((var(--red-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-red-light {
      --switch: calc((var(--red-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-red-dark {
      --switch: calc((var(--red-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-orange {
      --switch: calc((var(--orange-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-orange-light {
      --switch: calc((var(--orange-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-orange-dark {
      --switch: calc((var(--orange-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-yellow {
      --switch: calc((var(--yellow-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-yellow-light {
      --switch: calc((var(--yellow-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-yellow-dark {
      --switch: calc((var(--yellow-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-green {
      --switch: calc((var(--green-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-green-light {
      --switch: calc((var(--green-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-green-dark {
      --switch: calc((var(--green-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-teal {
      --switch: calc((var(--teal-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-teal-light {
      --switch: calc((var(--teal-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-teal-dark {
      --switch: calc((var(--teal-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-cyan {
      --switch: calc((var(--cyan-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-cyan-light {
      --switch: calc((var(--cyan-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-cyan-dark {
      --switch: calc((var(--cyan-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-gray {
      --switch: calc((var(--gray-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-gray-light {
      --switch: calc((var(--gray-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-gray-dark {
      --switch: calc((var(--gray-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }



    .text-n-graydark {
      --switch: calc((var(--graydark-color-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-graydark-light {
      --switch: calc((var(--graydark-color-light-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }
    .text-n-graydark-dark {
      --switch: calc((var(--graydark-color-dark-l) - var(--contrastThreshold)) * -100);
      color: hsl(0, 0%, var(--switch));
    }


    /*  Etc */
    .box-container {
      display: flex;
      flex-wrap: wrap;
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      justify-content: center;
    }

    .controls {
      margin-top: 1rem;
      padding: 1rem 2rem;
      border-color: #bbb;
      border-style: dashed;
    }

    .controls div {
      width: 100px;
      text-align: center;
      display: inline-block;
    }




  </style>

