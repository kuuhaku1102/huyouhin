<?php get_header(); ?>

<!-- Hero Section -->
<section class="hero hero-new">
    <div class="container">
        <div class="hero-grid">
            <!-- Left Content -->
            <div class="hero-content">
                <h1 class="hero-title-new">
                    あなたにぴったりの<br>
                    不用品回収サービスを探そう
                </h1>
                
                <!-- Search Box -->
                <div class="hero-search-box">
                    <select class="hero-search-input" id="prefecture-select" onchange="if(this.value) window.location.href=this.value">
                        <option value="">都道府県または市区町村からお選びください</option>
                        <?php
                        $prefectures = [
                            '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
                            '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
                            '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県',
                            '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
                            '鳥取県', '島根県', '岡山県', '広島県', '山口県',
                            '徳島県', '香川県', '愛媛県', '高知県',
                            '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
                        ];
                        foreach ($prefectures as $pref) {
                            $pref_url = home_url('/' . $pref . '/');
                            echo '<option value="' . esc_url($pref_url) . '">' . esc_html($pref) . '</option>';
                        }
                        ?>
                    </select>
                    <button class="hero-search-btn" onclick="document.getElementById('prefecture-select').onchange()">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        検索
                    </button>
                </div>

                <!-- Check Points -->
                <div class="hero-checks">
                    <div class="hero-check-item">
                        <svg class="check-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="#4caf50"/>
                            <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>明朗の目安料金に満足したい</span>
                    </div>
                    <div class="hero-check-item">
                        <svg class="check-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="#4caf50"/>
                            <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>サービスを比較し安く見つけたい</span>
                    </div>
                    <div class="hero-check-item">
                        <svg class="check-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" fill="#4caf50"/>
                            <path d="M8 12L11 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>安心して直接依頼できる業者を探したい</span>
                    </div>
                </div>
            </div>

            <!-- Right Illustration (Background) -->
            <div class="hero-illustration" style="background-image: url('<?php echo get_template_directory_uri(); ?>/images/hero-illustration.png');"></div>
        </div>

        <!-- Stats -->
        <div class="hero-stats-new">
            <div class="stat-item-new">
                <div class="stat-value-new">10,000+</div>
                <div class="stat-label-new">利用者数</div>
            </div>
            <div class="stat-item-new">
                <div class="stat-value-new">500+</div>
                <div class="stat-label-new">提携業者</div>
            </div>
            <div class="stat-item-new">
                <div class="stat-value-new">4.8</div>
                <div class="stat-label-new">平均評価</div>
            </div>
            <div class="stat-item-new">
                <div class="stat-value-new">98%</div>
                <div class="stat-label-new">満足度</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <h2 class="section-title">選ばれる理由</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3 class="feature-title">簡単30秒見積もり</h3>
                <p class="feature-description">サービス種別、物量、郵便番号を入力するだけで、複数の業者から見積もりを取得できます。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">信頼できるランキング</h3>
                <p class="feature-description">実際の利用者の評価と口コミに基づいた、公正なランキングを提供しています。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3 class="feature-title">厳選された優良業者</h3>
                <p class="feature-description">独自の審査基準をクリアした、信頼できる業者のみを掲載しています。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3 class="feature-title">最安値保証</h3>
                <p class="feature-description">複数業者を比較することで、最もお得な料金で不用品回収が可能です。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3 class="feature-title">即日対応可能</h3>
                <p class="feature-description">お急ぎの方にも対応。最短即日で不用品を回収できる業者をご紹介します。</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">📞</div>
                <h3 class="feature-title">24時間サポート</h3>
                <p class="feature-description">お問い合わせやご相談は24時間受付。安心してご利用いただけます。</p>
            </div>
        </div>
    </div>
</section>

<!-- Top Rankings Section -->
<section class="top-rankings">
    <div class="container">
        <h2 class="section-title">おすすめ業者ランキング</h2>
        
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'comp2';
        $media_table = $wpdb->prefix . 'comp2_media';
        
        // wp_comp2テーブルからTOP6を取得
        $companies = $wpdb->get_results("
            SELECT 
                c.*,
                m.attachment_id
            FROM {$table_name} c
            LEFT JOIN {$media_table} m ON c.company_id = m.company_id
            WHERE c.total_rating_star IS NOT NULL
            ORDER BY CAST(c.total_rating_star AS DECIMAL(3,2)) DESC
            LIMIT 6
        ");
        
        if (!empty($companies)) :
            $rank = 1;
        ?>
            <div class="top-ranking-cards">
                <?php foreach ($companies as $company) : 
                    // ロゴURLを取得
                    $logo_url = '';
                    if ($company->attachment_id) {
                        $logo_url = wp_get_attachment_image_url($company->attachment_id, 'medium');
                    }
                    if (!$logo_url && !empty($company->logo_image_url)) {
                        $logo_url = $company->logo_image_url;
                    }
                    
                    $rating = floatval($company->total_rating_star);
                ?>
                    <div class="top-ranking-card rank-<?php echo $rank; ?>">
                        <div class="rank-badge-top">
                            <span class="rank-number"><?php echo $rank; ?></span>
                        </div>
                        
                        <?php if (!empty($logo_url)) : ?>
                            <div class="company-logo">
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($company->company_name); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-content">
                            <h3 class="company-name"><?php echo esc_html($company->company_name); ?></h3>
                            
                            <?php if ($rating > 0) : ?>
                                <div class="company-rating">
                                    <div class="rating-stars">
                                        <?php 
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= floor($rating)) {
                                                echo '<span class="star filled">★</span>';
                                            } elseif ($i - 0.5 <= $rating) {
                                                echo '<span class="star half">★</span>';
                                            } else {
                                                echo '<span class="star empty">☆</span>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($company->price_info)) : ?>
                                <div class="company-price">
                                    <span class="price-label">料金:</span>
                                    <span class="price-value"><?php echo esc_html($company->price_info); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($company->service_area)) : ?>
                                <div class="company-areas">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14 6.66667C14 11.3333 8 15.3333 8 15.3333C8 15.3333 2 11.3333 2 6.66667C2 5.07536 2.63214 3.54926 3.75736 2.42404C4.88258 1.29882 6.40869 0.666672 8 0.666672C9.59131 0.666672 11.1174 1.29882 12.2426 2.42404C13.3679 3.54926 14 5.07536 14 6.66667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 8.66667C9.10457 8.66667 10 7.77124 10 6.66667C10 5.5621 9.10457 4.66667 8 4.66667C6.89543 4.66667 6 5.5621 6 6.66667C6 7.77124 6.89543 8.66667 8 8.66667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span><?php echo esc_html(mb_substr($company->service_area, 0, 30)); ?><?php echo mb_strlen($company->service_area) > 30 ? '...' : ''; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php echo home_url('/rankings/'); ?>" class="btn-card">
                                詳細を見る
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php 
                    $rank++;
                endforeach;
                ?>
            </div>
            
            <div class="section-cta">
                <a href="<?php echo home_url('/rankings'); ?>" class="btn btn-primary">すべてのランキングを見る</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Services Section -->
<section class="services">
    <div class="container">
        <h2 class="section-title">対応サービス</h2>
        
        <div class="services-grid">
            <div class="service-card">
                <h3 class="service-title">不用品回収</h3>
                <p class="service-description">家具、家電、日用品など、あらゆる不用品を回収します。</p>
            </div>
            
            <div class="service-card">
                <h3 class="service-title">遺品整理</h3>
                <p class="service-description">故人の遺品を丁寧に整理・処分いたします。</p>
            </div>
            
            <div class="service-card">
                <h3 class="service-title">ゴミ屋敷清掃</h3>
                <p class="service-description">大量のゴミや不用品の一括処分に対応します。</p>
            </div>
            
            <div class="service-card">
                <h3 class="service-title">生前整理</h3>
                <p class="service-description">将来に備えた整理整頓をサポートします。</p>
            </div>
        </div>
    </div>
</section>

<!-- Prefecture Links Section -->
<section class="prefecture-section">
    <div class="container">
        <h2 class="section-title">都道府県から業者を探す</h2>
        <p class="section-description">お住まいの地域で利用できる不用品回収業者を探す</p>
        
        <?php
        $prefecture_groups = array(
            '北海道・東北' => array('北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県'),
            '関東' => array('茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県'),
            '中部' => array('新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県'),
            '近畠' => array('滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県'),
            '中国・四国' => array('鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県'),
            '九州・沖縄' => array('福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県')
        );
        
        foreach ($prefecture_groups as $region => $prefectures) :
        ?>
            <div class="prefecture-group">
                <h3 class="prefecture-group-title"><?php echo esc_html($region); ?></h3>
                <div class="prefecture-links">
                    <?php
                    foreach ($prefectures as $prefecture) {
                        $page = get_page_by_title($prefecture, OBJECT, 'page');
                        if ($page) {
                            echo '<a href="' . get_permalink($page->ID) . '" class="prefecture-link">' . esc_html($prefecture) . '</a>';
                        } else {
                            echo '<span class="prefecture-link disabled">' . esc_html($prefecture) . '</span>';
                        }
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- About Section -->
<section class="seo-content about-section">
    <div class="container">
        <h2 class="section-title">不用品回収とは？</h2>
        <div class="content-text">
            <p>不用品回収とは、家庭やオフィスで不要になった家具、家電、日用品などを専門業者が回収・処分するサービスです。引っ越しや大掃除、遺品整理など、さまざまなシーンで利用されています。</p>
            <p>自治体の粗大ゴミ回収と比較して、<strong>即日対応が可能</strong>、<strong>搬出作業も依頼できる</strong>、<strong>回収品目の制限が少ない</strong>といったメリットがあります。特に、大量の不用品を一度に処分したい場合や、重い家具を運び出せない場合に便利なサービスです。</p>
            <p>当サイトでは、全国の優良な不用品回収業者を比較し、お客様に最適な業者をご紹介しています。複数の業者から見積もりを取ることで、<strong>料金を比較</strong>し、<strong>サービス内容を確認</strong>できるため、安心してご利用いただけます。</p>
        </div>
    </div>
</section>

<!-- Price Table Section -->
<section class="seo-content price-table-section">
    <div class="container">
        <h2 class="section-title">不用品回収の料金相場</h2>
        <p class="section-description">一般的な不用品回収の料金相場をご紹介します。実際の料金は、物量や地域、業者によって異なりますので、必ず見積もりを取ることをおすすめします。</p>
        
        <div class="price-table-wrapper">
            <table class="price-table">
                <thead>
                    <tr>
                        <th>間取り・物量目安</th>
                        <th>トラックサイズ</th>
                        <th>料金相場</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>単品（冷蔵庫、洗濯機など）</td>
                        <td>-</td>
                        <td>3,000円～8,000円</td>
                    </tr>
                    <tr>
                        <td>1R・1K（一人暮らし）</td>
                        <td>軽トラック</td>
                        <td>15,000円～30,000円</td>
                    </tr>
                    <tr>
                        <td>1DK・1LDK</td>
                        <td>1.5tトラック</td>
                        <td>30,000円～50,000円</td>
                    </tr>
                    <tr>
                        <td>2DK・2LDK</td>
                        <td>2tトラック</td>
                        <td>50,000円～80,000円</td>
                    </tr>
                    <tr>
                        <td>3DK・3LDK</td>
                        <td>2tトラック～</td>
                        <td>80,000円～150,000円</td>
                    </tr>
                    <tr>
                        <td>4LDK以上・一軒家</td>
                        <td>4tトラック～</td>
                        <td>150,000円～300,000円</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="price-note">
            <p><strong>※ 料金に含まれるもの：</strong>基本料金、車両費、人件費、処分費用</p>
            <p><strong>※ 追加料金が発生する場合：</strong>階段作業（2階以上）、エアコン取り外し、特殊な不用品（ピアノ、金庫など）</p>
        </div>
    </div>
</section>

<!-- Selection Points Section -->
<section class="seo-content selection-points-section">
    <div class="container">
        <h2 class="section-title">失敗しない業者選びのポイント</h2>
        <p class="section-description">優良な不用品回収業者を選ぶために、以下のポイントをチェックしましょう。</p>
        
        <div class="points-grid">
            <div class="point-card">
                <div class="point-number">1</div>
                <h3 class="point-title">許可証の確認</h3>
                <p class="point-description">「一般廃棄物収集運搬業許可」または「古物商許可」を持っているか確認しましょう。許可証のない業者は違法業者の可能性があります。</p>
            </div>
            
            <div class="point-card">
                <div class="point-number">2</div>
                <h3 class="point-title">明確な料金体系</h3>
                <p class="point-description">見積もり時に料金の内訳を明確に説明してくれる業者を選びましょう。追加料金の有無も事前に確認することが重要です。</p>
            </div>
            
            <div class="point-card">
                <div class="point-number">3</div>
                <h3 class="point-title">口コミ・評判</h3>
                <p class="point-description">実際に利用した人の口コミや評価を参考にしましょう。当サイトでは、実際の利用者の声を掲載しています。</p>
            </div>
            
            <div class="point-card">
                <div class="point-number">4</div>
                <h3 class="point-title">対応の丁寧さ</h3>
                <p class="point-description">電話やメールでの対応が丁寧か、質問に的確に答えてくれるかをチェックしましょう。対応の質はサービスの質に直結します。</p>
            </div>
            
            <div class="point-card">
                <div class="point-number">5</div>
                <h3 class="point-title">保険の加入</h3>
                <p class="point-description">万が一の事故に備えて、損害賠償保険に加入している業者を選びましょう。安心して作業を任せられます。</p>
            </div>
            
            <div class="point-card">
                <div class="point-number">6</div>
                <h3 class="point-title">複数社の比較</h3>
                <p class="point-description">1社だけでなく、複数の業者から見積もりを取って比較しましょう。料金だけでなく、サービス内容も比較することが大切です。</p>
            </div>
        </div>
    </div>
</section>

<!-- Flow Section -->
<section class="seo-content flow-section">
    <div class="container">
        <h2 class="section-title">ご利用の流れ</h2>
        <p class="section-description">当サイトでの不用品回収業者の選び方をご紹介します。</p>
        
        <div class="flow-steps">
            <div class="flow-step">
                <div class="step-number">STEP 1</div>
                <h3 class="step-title">見積もり依頼</h3>
                <p class="step-description">サービス種別、物量、郵便番号を入力して、簡匵30秒で一括見積もり依頼が完了します。</p>
            </div>
            
            <div class="flow-arrow">→</div>
            
            <div class="flow-step">
                <div class="step-number">STEP 2</div>
                <h3 class="step-title">業者から連絡</h3>
                <p class="step-description">複数の業者から見積もりが届きます。料金、サービス内容、対応日時を比較検討しましょう。</p>
            </div>
            
            <div class="flow-arrow">→</div>
            
            <div class="flow-step">
                <div class="step-number">STEP 3</div>
                <h3 class="step-title">業者を選ぶ</h3>
                <p class="step-description">比較検討した中から、最も条件の良い業者を選び、作業日時を確定します。</p>
            </div>
            
            <div class="flow-arrow">→</div>
            
            <div class="flow-step">
                <div class="step-number">STEP 4</div>
                <h3 class="step-title">作業完了</h3>
                <p class="step-description">約束の日時に業者が訪問し、不用品を回収します。作業完了後、料金をお支払いいただきます。</p>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Table Section -->
<section class="seo-content comparison-section">
    <div class="container">
        <h2 class="section-title">自治体vs業者の比較</h2>
        <p class="section-description">自治体の粗大ゴミ回収と不用品回収業者の違いを比較しましょう。</p>
        
        <div class="comparison-table-wrapper">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>項目</th>
                        <th>自治体（粗大ゴミ）</th>
                        <th>不用品回収業者</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>料金</td>
                        <td>安い（数百円～）</td>
                        <td>高め（数千円～）</td>
                    </tr>
                    <tr>
                        <td>回収速度</td>
                        <td>予約必要（数週間後）</td>
                        <td>即日対応可能</td>
                    </tr>
                    <tr>
                        <td>搬出作業</td>
                        <td>自分で運ぶ</td>
                        <td>業者が対応</td>
                    </tr>
                    <tr>
                        <td>回収品目</td>
                        <td>制限あり</td>
                        <td>ほぼ何でもOK</td>
                    </tr>
                    <tr>
                        <td>分別</td>
                        <td>自分で分別</td>
                        <td>業者が対応</td>
                    </tr>
                    <tr>
                        <td>手続き</td>
                        <td>シール購入・予約必要</td>
                        <td>電話一本でOK</td>
                    </tr>
                    <tr>
                        <td>大量処分</td>
                        <td>困難</td>
                        <td>対応可能</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="comparison-note">
            <p><strong>おすすめの使い分け：</strong></p>
            <ul>
                <li><strong>自治体：</strong>少量の不用品、時間に余裕がある、費用を抑えたい</li>
                <li><strong>業者：</strong>大量の不用品、急いでいる、搬出が困難、手間をかけたくない</li>
            </ul>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="seo-content faq-section">
    <div class="container">
        <h2 class="section-title">よくある質問</h2>
        <p class="section-description">不用品回収に関するよくある質問と回答をまとめました。</p>
        
        <div class="faq-list">
            <div class="faq-item">
                <h3 class="faq-question">不用品回収の料金はどのくらいかかりますか？</h3>
                <div class="faq-answer">
                    <p>料金は回収する不用品の量や種類、お住まいの地域によって異なります。単品であれば3,000円～8,000円程度、1R・1Kの一人暮らしであれづ15,000円～30,000円程度が目安です。詳しくは上記の料金相場表をご確認ください。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">即日対応は可能ですか？</h3>
                <div class="faq-answer">
                    <p>はい、多くの業者が即日対応可能です。ただし、繁忙期や土日祝日は予約が埋まっていることがありますので、お急ぎの場合は早めにご連絡ください。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">見積もりは無料ですか？</h3>
                <div class="faq-answer">
                    <p>はい、当サイトでご紹介する業者は、基本的に見積もり無料です。見積もり後にキャンセルしても、キャンセル料は発生しません。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">追加料金は発生しますか？</h3>
                <div class="faq-answer">
                    <p>基本的には見積もり時に提示された料金から変わりません。ただし、以下の場合は追加料金が発生することがあります：階段作業（2階以上）、エアコンの取り外し、特殊な不用品（ピアノ、金庫など）。事前に確認しておきましょう。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">何でも回収してもらえますか？</h3>
                <div class="faq-answer">
                    <p>ほとんどの不用品は回収可能ですが、以下のものは回収できない場合があります：生もの、危険物（ガスボンベ、火薬類）、医療廃棄物、車両、バイクなど。詳しくは業者にお問い合わせください。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">立ち会いは必要ですか？</h3>
                <div class="faq-answer">
                    <p>基本的には立ち会いが必要ですが、業者によっては不在時の作業に対応していることもあります。事前に業者に相談してください。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">支払い方法は何がありますか？</h3>
                <div class="faq-answer">
                    <p>多くの業者が現金、クレジットカード、銀行振込に対応しています。業者によっては電子マネーやQRコード決済にも対応しています。詳しくは各業者にお問い合わせください。</p>
                </div>
            </div>
            
            <div class="faq-item">
                <h3 class="faq-question">買取もしてもらえますか？</h3>
                <div class="faq-answer">
                    <p>はい、多くの業者が不用品の買取も行っています。状態の良い家電や家具、ブランド品などは買取対象となることがあり、回収費用から差し引きされる場合があります。</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <h2 class="cta-title">今すぐ無料で見積もりを取りましょう</h2>
        <p class="cta-description">複数の業者を比較して、最適な不用品回収業者を見つけましょう。</p>
        <a href="<?php echo home_url('/quote'); ?>" class="btn btn-primary btn-large">無料見積もりを取る</a>
    </div>
</section>

<?php get_footer(); ?>
