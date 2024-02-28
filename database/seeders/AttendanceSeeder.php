<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\WorkSchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $yesterday = now()->subDay()->format('Y-m-d');
        $yestedayWorkSchedId = WorkSchedule::where('date', $yesterday)->first()->id;

        $descriptionSamples = collect([
            'Instagram・ブログ記事作成',
            'サビ管イラスト、インスタ個人アカウント、企画デザイン',
            'ワライフ',
            'ワライフ・Instagram・ブログ記事作成',
            '【パパイヤ知美】フォロー数 7 / コメ数 4 【はじめの健康】リポスト業務 レシピ検索 ワライフ案件 インスタグラム 社員紹介（石見さん）投稿 インスタグラム 【ワライフ/サイヨウ】 アンフォロー50件 【ワライフ/シンジュク】フォロー 24 コメント 24',
            '【パパイヤ知美】フォロー数 5 / コメ数 5 【はじめの健康】リポスト業務 レシピ検索 ワライフ案件 インスタグラム 他己紹介（星さん）ストーリーズ投稿 ストーリーズ投稿作成  インスタグラム投稿についてチームでの話し合い インスタグラム 【ワライフ/サイヨウ】フォロー 5 コメント 4 アンフォロー50件 【ワライフ/シンジュク】フォロー 5 コメント 5',
            '【パパイヤ知美】フォロー数 3 / コメ数 3 フォロー確認 【はじめの健康】リポスト業務 レシピ検索 ワライフ案件 インスタグラム 他己紹介（星さん）投稿 インスタグラム ストーリーズ作成 TODOカレンダー調整',
        ]);
        $commentSamples = collect([
            '【体調や作業について】 午前中の作業でした 明日頑張ります。',
            '【体調や作業について】 なんか頭が鈍く痛くなりました。 作業しにくなりました。 【その他】 特になし',
            '【体調や作業について】 頭が鈍く痛くなりました。 午後の作業がしんどくなりました。 【その他】 特になし',
            '【作業面】特別支援学校について菅沼さんに色々なアドバイスを貰えたので、明日加筆修正したいと思います 【バイノマ】フォロー数：１１／コメント数：６ 【パパイヤ知美】フォロー数：１３／コメント数：５ 【個人インスタ】<やったこと>新規フォロワー活動 【残業】１時間 【体調面】特に問題なし',
            '【作業面】engageのタイトル作成の説明がうまく出来なかったのが残念です 
【残業】なし 
【体調面】昨夜は不眠で、睡眠薬を飲んだからか、作業中も眠気が残っていたように感じます',
            '【作業面】特別支援学校の記事に使う写真が難しいです。学校っぽい写真自体は探せるのですが、喜屋武さんを組み込むとなるとなかなか難易度が高いです 
【個人インスタ】<やったこと>新規フォロワー活動 
【バイノマ】フォロー数：１０／コメント数：５ 
【パパイヤ知美】フォロー数：１０／コメント数：５ 
【残業】１時間
【体調面】特にないです',
        ]);

        for ($userId = 1; $userId <= 3; $userId++) {
            for ($workSchedId = 1; $workSchedId <= $yestedayWorkSchedId; $workSchedId++) {

                $schedTypeId = WorkSchedule::find($workSchedId)->schedule_type_id;
                if ($schedTypeId == "1") {
                    Attendance::factory()->create([
                        'work_schedule_id' => $workSchedId,
                        'user_id' => $userId,
                        'work_description' => $descriptionSamples->random(),
                        'work_comment' => $commentSamples->random(),
                    ]);
                }
            }
        }
    }
}
