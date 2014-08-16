<?php if(!class_exists('Rain\Tpl')){exit;}?><div id="div_content">
    <div class="row">
        <?php require $this->checkTemplate(''.htmlspecialchars( $page_menu, ENT_COMPAT, 'UTF-8', FALSE ));?>

        <div class="col-md-10 mtop-10">
            <div class="row">
                <div class="col-md-12">
                    <h3>test <span class="text-muted">test</span></h3>
                    <h5>test test</h5>
                    <hr/>
                    <h5>log_error</h5>
                    <ul>
                        <?php $counter1=-1;  if( isset($log_error_value) && ( is_array($log_error_value) || $log_error_value instanceof Traversable ) && sizeof($log_error_value) ) foreach( $log_error_value as $key1 => $value1 ){ $counter1++; ?>

                        <li><?php echo htmlspecialchars( $value1->nome, ENT_COMPAT, 'UTF-8', FALSE ); ?></li>
                        <?php } ?>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>