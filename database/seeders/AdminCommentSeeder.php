<?php

namespace Database\Seeders;

use App\Models\AdminComment;
use App\Models\Attendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attendances = Attendance::all();

        $descriptionSamples = collect([
            'ブログ、インスタ',
            'ブログ、インスタ、外部案件',
            '外部案件',
            'ワライフ・Instagram・ブログ記事作成',
            '(在宅)企業案件　インスタ',
            'インスタ',
            'イラスト制作',
        ]);
        $commentSamples = collect([
            '順調に作業を進められている。',
            '問題なく作業を進められる。ブログの修正箇所で悩んでいてた様子改めて一緒に内容を確認し対応してもらっています。',
            '同じ案件のメンバーへのフォローをしながら作業を進めていた。13:30頃「鈍く頭痛してきついです」とメッセージあり、無理なく作業をしてもらう。',
            'ワライフ案件、他メンバーとコミュニケーションを図りながら円滑に業務を進められている。表情もよくメンタル面では特に問題はない様子。',
            '昼食時は庭にでてのんびりと食事をしている。仕事に関しては、焦らないようにお仕事を進めているとのこと。',
            '本日無事朝から出勤できている。元気な様子が見られて安心。',
        ]);


        foreach ($attendances as $attendance) {
            $newData = AdminComment::factory()->make()->toArray();
            $newData['admin_description'] = $descriptionSamples->random();
            $newData['admin_comment'] = $commentSamples->random();
            AdminComment::updateOrCreate(
                ['attendance_id' => $attendance->id],
                $newData
            );
        }
    }
}
