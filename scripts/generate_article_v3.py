"""
記事生成エンジン v3.0 - 6ステップ品質管理プロセス
Gemini APIを使用して高品質なSEO記事を生成します。
"""

import os
import json
from typing import Dict, List
from openai import OpenAI

class ArticleGenerator:
    def __init__(self):
        # OpenAI API設定
        self.client = OpenAI(
            api_key=os.environ.get("OPENAI_API_KEY")
        )
        self.model = "gpt-4o-mini"
        self.pass_score = 50  # 品質ゲートの合格点
    
    def generate_article(self, category_data: Dict, role_data: Dict) -> Dict:
        """6ステップで記事を生成"""
        print(f"\n{'='*60}")
        print(f"📝 記事生成開始: {role_data['role']}")
        print(f"{'='*60}\n")
        
        # Step 1: 検索意図の定義
        intent_data = self.step1_define_intent(category_data, role_data)
        
        # Step 2: 見出し構造の設計
        outline = self.step2_design_outline(intent_data, category_data, role_data)
        
        # Step 3: セクション単位での本文生成
        sections = self.step3_generate_sections(outline, intent_data, category_data, role_data)
        
        # Step 4: 全文の統合
        full_article = self.step4_integrate_article(sections, outline)
        
        # Step 5: 品質ゲート（自動審査）
        quality_score = self.step5_quality_gate(full_article, intent_data)
        
        if quality_score < self.pass_score:
            raise Exception(f"❌ 品質基準を満たしていません（スコア: {quality_score}/{self.pass_score}）")
        
        # Step 6: SEO最適化パッケージ
        seo_package = self.step6_seo_optimization(full_article, category_data, role_data)
        
        return {
            "content": full_article,
            "seo": seo_package,
            "quality_score": quality_score,
            "category": category_data["slug"],
            "role": role_data["role"]
        }
    
    def step1_define_intent(self, category_data: Dict, role_data: Dict) -> Dict:
        """検索意図の定義"""
        print("📌 Step 1: 検索意図の定義")
        
        # キーワードが指定されている場合はそれを使用
        keyword_instruction = ""
        if 'selected_keyword' in role_data:
            keyword_instruction = f"\n【ターゲットキーワード】{role_data['selected_keyword']}\n※このキーワードを中心に記事を構成してください。"
        
        prompt = f"""
あなたはSEOの専門家です。以下の記事テーマについて、検索意図を分析してください。

【カテゴリー】{category_data['name']}
【記事の役割】{role_data['role']}
【目的】{role_data['purpose']}
【差別化ポイント】{role_data['differentiation']}{keyword_instruction}

以下の項目をJSON形式で出力してください：
{{
  "primary_intent": "主要な検索意図（Know/Do/Go）",
  "user_pain_points": ["ユーザーの悩み1", "悩み2", "悩み3"],
  "expected_value": "記事から得られる価値",
  "target_keywords": ["メインキーワード", "サブキーワード1", "サブキーワード2"]
}}
"""
        
        response = self.client.chat.completions.create(
            model=self.model,
            messages=[{"role": "user", "content": prompt}],
            temperature=0.7
        )
        
        intent_json = response.choices[0].message.content
        # JSONブロックを抽出
        if "```json" in intent_json:
            intent_json = intent_json.split("```json")[1].split("```")[0].strip()
        elif "```" in intent_json:
            intent_json = intent_json.split("```")[1].split("```")[0].strip()
        
        intent_data = json.loads(intent_json)
        print(f"  ✓ 検索意図: {intent_data['primary_intent']}")
        print(f"  ✓ ターゲットキーワード: {', '.join(intent_data['target_keywords'])}")
        
        return intent_data
    
    def step2_design_outline(self, intent_data: Dict, category_data: Dict, role_data: Dict) -> List[Dict]:
        """Step 2: 見出し構造の設計"""
        print("\n📌 Step 2: 見出し構造の設計")
        
        prompt = f"""
以下の情報を元に、SEOに最適化された記事の見出し構造を設計してください。

【検索意図】{intent_data['primary_intent']}
【ユーザーの悩み】{', '.join(intent_data['user_pain_points'])}
【記事の役割】{role_data['role']}
【差別化ポイント】{role_data['differentiation']}

要件：
- H2見出しを5〜7個作成
- 各H2の下にH3見出しを2〜4個作成
- 見出しには自然にキーワードを含める
- 論理的な流れを意識する

以下の形式のJSON配列で出力してください：
[
  {{
    "h2": "見出し2のテキスト",
    "h3_list": ["見出し3-1", "見出し3-2", "見出し3-3"]
  }},
  ...
]
"""
        
        response = self.client.chat.completions.create(
            model=self.model,
            messages=[{"role": "user", "content": prompt}],
            temperature=0.7
        )
        
        outline_json = response.choices[0].message.content
        if "```json" in outline_json:
            outline_json = outline_json.split("```json")[1].split("```")[0].strip()
        elif "```" in outline_json:
            outline_json = outline_json.split("```")[1].split("```")[0].strip()
        
        outline = json.loads(outline_json)
        print(f"  ✓ H2見出し数: {len(outline)}")
        for i, section in enumerate(outline, 1):
            print(f"    {i}. {section['h2']} ({len(section['h3_list'])}個のH3)")
        
        return outline
    
    def step3_generate_sections(self, outline: List[Dict], intent_data: Dict, 
                                category_data: Dict, role_data: Dict) -> List[Dict]:
        """Step 3: セクション単位での本文生成"""
        print("\n📌 Step 3: セクション単位での本文生成")
        
        sections = []
        
        for i, section_outline in enumerate(outline, 1):
            print(f"  📝 セクション {i}/{len(outline)}: {section_outline['h2']}")
            
            prompt = f"""
以下の見出しに対応する本文を生成してください。

【H2見出し】{section_outline['h2']}
【H3見出しリスト】{', '.join(section_outline['h3_list'])}
【検索意図】{intent_data['primary_intent']}
【差別化ポイント】{role_data['differentiation']}

要件：
- 各H3セクションは300〜500文字程度
- 具体的な情報と実例を含める
- 読みやすい文章構成
- 専門用語は適切に説明
- Markdown形式で出力

出力形式：
## {section_outline['h2']}

### {section_outline['h3_list'][0]}
（本文）

### {section_outline['h3_list'][1]}
（本文）

...
"""
            
            response = self.client.chat.completions.create(
                model=self.model,
                messages=[{"role": "user", "content": prompt}],
                temperature=0.7
            )
            
            section_content = response.choices[0].message.content
            # Markdownブロックを抽出
            if "```markdown" in section_content:
                section_content = section_content.split("```markdown")[1].split("```")[0].strip()
            elif "```" in section_content:
                section_content = section_content.split("```")[1].split("```")[0].strip()
            
            sections.append({
                "h2": section_outline["h2"],
                "content": section_content
            })
        
        print(f"  ✓ {len(sections)}個のセクションを生成完了")
        return sections
    
    def step4_integrate_article(self, sections: List[Dict], outline: List[Dict]) -> str:
        """Step 4: 全文の統合"""
        print("\n📌 Step 4: 全文の統合")
        
        # イントロダクションを生成
        intro_prompt = f"""
以下の記事の導入部分（リード文）を200〜300文字で生成してください。

【記事の見出し構成】
{chr(10).join([f"- {s['h2']}" for s in outline])}

要件：
- 読者の関心を引く
- 記事の価値を明確に伝える
- 自然な文章
"""
        
        intro_response = self.client.chat.completions.create(
            model=self.model,
            messages=[{"role": "user", "content": intro_prompt}],
            temperature=0.7
        )
        
        intro = intro_response.choices[0].message.content.strip()
        
        # 全文を統合
        full_article = intro + "\n\n"
        
        for section in sections:
            full_article += section["content"] + "\n\n"
        
        # まとめを生成
        conclusion_prompt = f"""
以下の記事のまとめ（結論）を200〜300文字で生成してください。

【記事の見出し構成】
{chr(10).join([f"- {s['h2']}" for s in outline])}

要件：
- 記事の要点を簡潔にまとめる
- 行動を促す
- 前向きな印象
"""
        
        conclusion_response = self.client.chat.completions.create(
            model=self.model,
            messages=[{"role": "user", "content": conclusion_prompt}],
            temperature=0.7
        )
        
        conclusion = conclusion_response.choices[0].message.content.strip()
        
        full_article += "## まとめ\n\n" + conclusion
        
        word_count = len(full_article.replace(" ", "").replace("\n", ""))
        print(f"  ✓ 全文統合完了（約{word_count}文字）")
        
        return full_article
    
    def step5_quality_gate(self, article: str, intent_data: Dict) -> int:
        """Step 5: 品質ゲート（自動審査）"""
        print("\n📌 Step 5: 品質ゲート（自動審査）")
        
        prompt = f"""
以下の記事を品質評価してください。

【記事本文】
{article[:3000]}...

【評価基準】
1. 情報の正確性と客観性（20点）
2. 網羅性と深さ（20点）
3. 読みやすさと構成（20点）
4. SEO最適化（20点）
5. 独自性と差別化（20点）

合計100点満点で評価し、以下のJSON形式で出力してください：
{{
  "total_score": 85,
  "accuracy": 18,
  "comprehensiveness": 17,
  "readability": 19,
  "seo": 16,
  "uniqueness": 15,
  "feedback": "評価コメント"
}}
"""
        
        response = self.client.chat.completions.create(
            model=self.model,
            messages=[{"role": "user", "content": prompt}],
            temperature=0.3
        )
        
        quality_json = response.choices[0].message.content
        if "```json" in quality_json:
            quality_json = quality_json.split("```json")[1].split("```")[0].strip()
        elif "```" in quality_json:
            quality_json = quality_json.split("```")[1].split("```")[0].strip()
        
        quality_data = json.loads(quality_json)
        
        print(f"  ✓ 品質スコア: {quality_data['total_score']}/100")
        print(f"    - 正確性: {quality_data['accuracy']}/20")
        print(f"    - 網羅性: {quality_data['comprehensiveness']}/20")
        print(f"    - 読みやすさ: {quality_data['readability']}/20")
        print(f"    - SEO: {quality_data['seo']}/20")
        print(f"    - 独自性: {quality_data['uniqueness']}/20")
        
        return quality_data['total_score']
    
    def step6_seo_optimization(self, article: str, category_data: Dict, role_data: Dict) -> Dict:
        """Step 6: SEO最適化パッケージ"""
        print("\n📌 Step 6: SEO最適化パッケージ")
        
        prompt = f"""
以下の記事に対して、SEO最適化パッケージを生成してください。

【記事本文（抜粋）】
{article[:2000]}...

【カテゴリー】{category_data['name']}
【記事の役割】{role_data['role']}

以下のJSON形式で出力してください：
{{
  "title_candidates": ["タイトル案1", "タイトル案2", "タイトル案3", "タイトル案4", "タイトル案5"],
  "meta_description": "メタディスクリプション（120〜160文字）",
  "focus_keywords": ["メインキーワード", "サブキーワード1", "サブキーワード2"],
  "faq": [
    {{"question": "よくある質問1", "answer": "回答1"}},
    {{"question": "よくある質問2", "answer": "回答2"}},
    {{"question": "よくある質問3", "answer": "回答3"}}
  ]
}}
"""
        
        response = self.client.chat.completions.create(
            model=self.model,
            messages=[{"role": "user", "content": prompt}],
            temperature=0.7
        )
        
        seo_json = response.choices[0].message.content
        if "```json" in seo_json:
            seo_json = seo_json.split("```json")[1].split("```")[0].strip()
        elif "```" in seo_json:
            seo_json = seo_json.split("```")[1].split("```")[0].strip()
        
        seo_package = json.loads(seo_json)
        
        print(f"  ✓ タイトル案: {len(seo_package['title_candidates'])}個生成")
        print(f"  ✓ メタディスクリプション: {len(seo_package['meta_description'])}文字")
        print(f"  ✓ FAQ: {len(seo_package['faq'])}個生成")
        
        return seo_package


if __name__ == "__main__":
    # テスト実行
    generator = ArticleGenerator()
    
    test_category = {
        "slug": "basics",
        "name": "不用品回収の基礎知識"
    }
    
    test_role = {
        "role": "基礎知識記事",
        "purpose": "初心者向けの基本情報を提供",
        "differentiation": "実例と具体的な数字を豊富に使用"
    }
    
    article = generator.generate_article(test_category, test_role)
    print(f"\n{'='*60}")
    print(f"✅ 記事生成完了")
    print(f"  品質スコア: {article['quality_score']}/100")
    print(f"  タイトル案: {article['seo']['title_candidates'][0]}")
    print(f"{'='*60}")
