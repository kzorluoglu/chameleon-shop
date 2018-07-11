<?php
/*@var $oReview TdbShopArticleReview */
/*@var $aCallTimeVars array */
$oMsgManager = TCMSMessageManager::GetInstance();
$sCaptchaQuestion = '';
$bNeedUserFieldForName = false;
$iRatingStars = 5;
$oActiveArticle = TdbShop::GetActiveItem();
if ($oActiveArticle->IsVariant()) {
    $oActiveArticle = $oActiveArticle->GetFieldVariantParent();
}
if (array_key_exists('sCaptchaQuestion', $aCallTimeVars)) {
    $sCaptchaQuestion = $aCallTimeVars['sCaptchaQuestion'];
}
if (array_key_exists('aUserData', $aCallTimeVars)) {
    $aUserData = $aCallTimeVars['aUserData'];
}
if (array_key_exists('bNeedUserFieldForName', $aCallTimeVars)) {
    $bNeedUserFieldForName = $aCallTimeVars['bNeedUserFieldForName'];
}
if (array_key_exists('iRatingStars', $aCallTimeVars)) {
    $iRatingStars = $aCallTimeVars['iRatingStars'];
}
$oUser = TdbDataExtranetUser::GetInstance();
?>
<input type="hidden" name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME).'[data_extranet_user_id]'; ?>"
       value="<?=$oUser->id; ?>"/>
<table class="standardtable">
    <?php if (isset($bNeedUserFieldForName) && $bNeedUserFieldForName) {
    ?>
    <tr>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_shop_article_review.form.author_name')).':'; ?></th>
        <td>
            <input type="text" class="userinput"
                   name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME).'[author_name]'; ?>"
                   value="<?=TGlobal::OutHTML($aUserData['author_name']); ?>"/>
        </td>
    </tr>
    <?php
} else {
        ?>
    <input type="hidden" name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME).'[author_name]'; ?>"
           value="<?=TGlobal::OutHTML($aUserData['author_name']); ?>"/>
    <?php
    }?>
    <?php if ($oUser->IsLoggedIn()) {
        ?>
    <input type="hidden" name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME).'[author_email]'; ?>"
           value="<?=TGlobal::OutHTML($oUser->fieldName); ?>"/>
    <?php
    } else {
        ?>
    <tr>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_shop_article_review.form.author_email')).':'; ?></th>
        <td>
            <input type="text" class="userinput"
                   name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME).'[author_email]'; ?>"
                   value="<?=TGlobal::OutHTML($aUserData['author_email']); ?>"/>
        </td>
    </tr>
    <?php
    } ?>
    <tr>
        <th><?=TGlobal::OutHtml(TGlobal::Translate('chameleon_system_shop_article_review.form.send_notifications')); ?></th>
        <td><input type="checkbox"
                   name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME).'[send_comment_notification]'; ?>" <?php if ('on' == $aUserData['send_comment_notification']) {
        echo 'checked="checked"';
    }?>></td>
    </tr>
    <tr>
        <th><?=TGlobal::OutHtml(TGlobal::Translate('chameleon_system_shop_article_review.form.title')); ?></th>
        <td><input type="text" class="userinput"
                   name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME).'[title]'; ?>"
                   value="<?=TGlobal::OutHTML($aUserData['title']); ?>"/></td>
    </tr>
    <tr>
        <th><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_shop_article_review.form.rating')).': *'; ?></th>
        <td>
            <?php
            echo'<div class="starsContainer">';
            for ($iRating = $iRatingStars; $iRating > 0; --$iRating) {
                echo '<div>';
                $sChecked = '';
                if ($iRating == $aUserData['rating']) {
                    $sChecked = 'checked ="checked"';
                }
                echo '<label><input class="reviewRadioButton plain" type="radio" name="'.TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME)."[rating]\" value=\"{$iRating}\" ".$sChecked.' />';
                for ($iTmp = 0; $iTmp < $iRating; ++$iTmp) {
                    echo '<img src="/static/images/star.png" alt="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_shop_article_review.form.rating_star')).'" border="0" />';
                }
                echo '</label>';
                echo '</div>';
            }
            echo'</div>';
            ?>
        </td>
    </tr>
    <tr>
        <th class="comment"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_shop_article_review.form.comment')); ?>: *</th>
        <td>
            <?php
            $error_style = '';
            if ($oMsgManager->ConsumerHasMessages(TdbShopArticleReview::MSG_CONSUMER_BASE_NAME.'-comment')) {
                echo $oMsgManager->RenderMessages(TdbShopArticleReview::MSG_CONSUMER_BASE_NAME.'-comment');
                $error_style = ' style="border:1px solid red"';
            }
            ?>
            <textarea class="userinput"<?=$error_style; ?> rows="5" cols="40"
                      name="<?=TGlobal::OutHTML(TdbShopArticleReview::INPUT_BASE_NAME); ?>[comment]"><?=TGlobal::OutHTML($aUserData['comment']); ?></textarea>
        </td>
    </tr>
    <?php if (!empty($sCaptchaQuestion)) {
                ?>
    <tr>
        <th class="comment"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_shop_article_review.form.captcha')); ?>: *</th>
        <td>
            <div class="floatleft">
                <?=TGlobal::OutHTML($sCaptchaQuestion); ?>
                <?php
                $error_style = '';
                if ($oMsgManager->ConsumerHasMessages(TdbShopArticleReview::MSG_CONSUMER_BASE_NAME.'-captcha')) {
                    echo $oMsgManager->RenderMessages(TdbShopArticleReview::MSG_CONSUMER_BASE_NAME.'-captcha');
                    $error_style = ' style="border:1px solid red"';
                } ?>
                <input type="text" class="userinput spamprotection"<?=$error_style; ?>
                       name="<?=TdbShopArticleReview::INPUT_BASE_NAME; ?>[captcha]" value=""/>
            </div>
            <div class="infotext floatleft" style="margin:0 0 0 20px; width: 250px;">
                (<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_shop_article_review.form.captcha_help')); ?>
                )
            </div>
            <div class="cleardiv">&nbsp;</div>
        </td>
    </tr>
    <?php
            } ?>
</table>



