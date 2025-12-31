<?php
/**
 * Template Name: Quote Form Page
 */
get_header();
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-title">無料一括見積もり</h1>
        <p class="page-description">簡単3ステップで複数の業者から見積もりを取得</p>
    </div>
</div>

<div class="quote-page">
    <div class="container">
        <div class="quote-form-wrapper">
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">サービス選択</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">物量入力</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">お客様情報</div>
                </div>
            </div>

            <!-- Quote Form -->
            <form id="quote-form" class="quote-form">
                <!-- Step 1: Service Type -->
                <div class="form-step active" data-step="1">
                    <h2 class="step-title">ご希望のサービスを選択してください</h2>
                    
                    <div class="service-options">
                        <label class="service-option">
                            <input type="radio" name="service_type" value="disposal" required>
                            <div class="option-card">
                                <div class="option-icon">🗑️</div>
                                <div class="option-title">不用品回収</div>
                                <div class="option-description">家具・家電・日用品など</div>
                            </div>
                        </label>

                        <label class="service-option">
                            <input type="radio" name="service_type" value="estate" required>
                            <div class="option-card">
                                <div class="option-icon">🏠</div>
                                <div class="option-title">遺品整理</div>
                                <div class="option-description">故人の遺品整理</div>
                            </div>
                        </label>

                        <label class="service-option">
                            <input type="radio" name="service_type" value="hoarding" required>
                            <div class="option-card">
                                <div class="option-icon">🧹</div>
                                <div class="option-title">ゴミ屋敷清掃</div>
                                <div class="option-description">大量のゴミ・不用品</div>
                            </div>
                        </label>

                        <label class="service-option">
                            <input type="radio" name="service_type" value="premortem" required>
                            <div class="option-card">
                                <div class="option-icon">📦</div>
                                <div class="option-title">生前整理</div>
                                <div class="option-description">将来に備えた整理</div>
                            </div>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-primary btn-next">次へ</button>
                    </div>
                </div>

                <!-- Step 2: Volume -->
                <div class="form-step" data-step="2">
                    <h2 class="step-title">おおよその物量を選択してください</h2>
                    
                    <div class="volume-options">
                        <label class="volume-option">
                            <input type="radio" name="volume_type" value="light_truck" required>
                            <div class="option-card">
                                <div class="option-icon">🚚</div>
                                <div class="option-title">軽トラック1台分</div>
                                <div class="option-description">段ボール10箱程度</div>
                            </div>
                        </label>

                        <label class="volume-option">
                            <input type="radio" name="volume_type" value="1t_truck" required>
                            <div class="option-card">
                                <div class="option-icon">🚛</div>
                                <div class="option-title">1tトラック1台分</div>
                                <div class="option-description">1K〜1DK程度</div>
                            </div>
                        </label>

                        <label class="volume-option">
                            <input type="radio" name="volume_type" value="2t_truck" required>
                            <div class="option-card">
                                <div class="option-icon">🚚</div>
                                <div class="option-title">2tトラック1台分</div>
                                <div class="option-description">1DK〜2DK程度</div>
                            </div>
                        </label>

                        <label class="volume-option">
                            <input type="radio" name="volume_type" value="2t_truck_2" required>
                            <div class="option-card">
                                <div class="option-icon">🚛</div>
                                <div class="option-title">2tトラック2台分以上</div>
                                <div class="option-description">2LDK以上</div>
                            </div>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline btn-prev">戻る</button>
                        <button type="button" class="btn btn-primary btn-next">次へ</button>
                    </div>
                </div>

                <!-- Step 3: Customer Info -->
                <div class="form-step" data-step="3">
                    <h2 class="step-title">お客様情報を入力してください</h2>
                    
                    <div class="form-fields">
                        <div class="form-group">
                            <label for="postal_code">郵便番号 <span class="required">*</span></label>
                            <input type="text" id="postal_code" name="postal_code" placeholder="例: 1000001" required>
                        </div>

                        <div class="form-group">
                            <label for="name">お名前 <span class="required">*</span></label>
                            <input type="text" id="name" name="name" placeholder="山田 太郎" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">電話番号 <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" placeholder="090-1234-5678" required>
                        </div>

                        <div class="form-group">
                            <label for="email">メールアドレス</label>
                            <input type="email" id="email" name="email" placeholder="example@email.com">
                        </div>

                        <div class="form-group">
                            <label for="preferred_date">希望日時</label>
                            <input type="text" id="preferred_date" name="preferred_date" placeholder="例: 2024年1月15日 午前中">
                        </div>

                        <div class="form-group">
                            <label for="notes">備考・ご要望</label>
                            <textarea id="notes" name="notes" rows="4" placeholder="その他ご要望があればご記入ください"></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline btn-prev">戻る</button>
                        <button type="submit" class="btn btn-primary btn-submit">見積もりを依頼する</button>
                    </div>
                </div>
            </form>

            <!-- Success Message -->
            <div id="success-message" class="success-message" style="display: none;">
                <div class="success-icon">✓</div>
                <h2>見積もり依頼を受け付けました</h2>
                <p>ご登録いただいた連絡先に、業者から見積もりが届きます。</p>
                <a href="<?php echo home_url(); ?>" class="btn btn-primary">トップページに戻る</a>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentStep = 1;
    const totalSteps = 3;

    // Next button
    $('.btn-next').on('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            updateStep();
        }
    });

    // Previous button
    $('.btn-prev').on('click', function() {
        currentStep--;
        updateStep();
    });

    // Form submission
    $('#quote-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateStep(currentStep)) {
            return;
        }

        const formData = {
            action: 'submit_quote',
            nonce: fuyohinAjax.nonce,
            service_type: $('input[name="service_type"]:checked').val(),
            volume_type: $('input[name="volume_type"]:checked').val(),
            postal_code: $('#postal_code').val(),
            name: $('#name').val(),
            phone: $('#phone').val(),
            email: $('#email').val(),
            preferred_date: $('#preferred_date').val(),
            notes: $('#notes').val()
        };

        $.ajax({
            url: fuyohinAjax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('.quote-form-wrapper').hide();
                    $('#success-message').fadeIn();
                } else {
                    alert('エラーが発生しました: ' + response.data.message);
                }
            },
            error: function() {
                alert('通信エラーが発生しました。もう一度お試しください。');
            }
        });
    });

    function updateStep() {
        // Update progress steps
        $('.progress-steps .step').removeClass('active completed');
        $('.progress-steps .step').each(function() {
            const stepNum = $(this).data('step');
            if (stepNum < currentStep) {
                $(this).addClass('completed');
            } else if (stepNum === currentStep) {
                $(this).addClass('active');
            }
        });

        // Update form steps
        $('.form-step').removeClass('active');
        $('.form-step[data-step="' + currentStep + '"]').addClass('active');
    }

    function validateStep(step) {
        const currentFormStep = $('.form-step[data-step="' + step + '"]');
        const requiredInputs = currentFormStep.find('[required]');
        let isValid = true;

        requiredInputs.each(function() {
            if ($(this).is(':radio')) {
                const name = $(this).attr('name');
                if (!$('input[name="' + name + '"]:checked').length) {
                    isValid = false;
                    alert('選択してください');
                    return false;
                }
            } else if (!$(this).val()) {
                isValid = false;
                $(this).focus();
                alert('必須項目を入力してください');
                return false;
            }
        });

        return isValid;
    }
});
</script>

<?php get_footer(); ?>
