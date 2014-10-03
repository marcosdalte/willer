<?php if(!class_exists('Rain\Tpl')){exit;}?><!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="robots" content="noindex, nofollow">
        <link href="<?php echo TEMPLATE_PATH; ?>/<?php echo htmlspecialchars( $template, ENT_COMPAT, 'UTF-8', FALSE ); ?>/css/bootstrap.css" rel="stylesheet" type="text/css" media="screen">
        <link href="<?php echo TEMPLATE_PATH; ?>/<?php echo htmlspecialchars( $template, ENT_COMPAT, 'UTF-8', FALSE ); ?>/css/bootstrap-theme.css" rel="stylesheet" type="text/css" media="screen">
        <link href="<?php echo PUBLIC_PATH; ?>/css/style.css" rel="stylesheet" type="text/css" media="screen">
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <![endif]-->
        <title>Doc</title>
    </head>
    <body>
        <!-- navbar top -->
        <div class="navbar navbar-collapse navbar-fixed-top navbar-inverse" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?php echo URL_BASE; ?>/home">willer</a>
                </div>
            </div>
        </div>
        <!-- container main -->
        <div class="container" style="margin-top:60px">
            <?php require $this->checkTemplate(''.htmlspecialchars( $page_view, ENT_COMPAT, 'UTF-8', FALSE ));?>

        </div>
        <!-- footer -->
        <div class="footer">
            <div class="container">
                <footer>
                    <div class="col-md-12">
                        <p></p>
                    </div>
                </footer>
            </div>
        </div>
        <!-- load script -->
        <script type="text/javascript" src="<?php echo PUBLIC_PATH; ?>/vendor/jquery/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="<?php echo TEMPLATE_PATH; ?>/<?php echo htmlspecialchars( $template, ENT_COMPAT, 'UTF-8', FALSE ); ?>/js/bootstrap.min.js"></script>
        <!-- view js -->
        <script type="text/javascript">
            var url_base = "<?php echo URL_BASE; ?>";

        </script>
    </body>
</html>