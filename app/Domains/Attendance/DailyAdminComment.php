<?php

namespace App\Domains\Attendance;

use App\Models\AdminComment;

class DailyAdminComment
{
    protected array $dailyAdminComments;

    public function __construct()
    {
        $this->dailyAdminComments = [];
    }

    public function getDailyAdminComments(): array
    {
        return $this->dailyAdminComments;
    }

    /**
     * Push a AdminComment to the list
     *
     *@param AdminComment $adminComment The adminCooment to add
     *@return void
     */
    public function push(AdminComment $adminComment): void
    {
        $this->dailyAdminComments[] = $adminComment;
    }
    /** 
     *管理者コメントを業務内容:コメント の形式で表示。2つ以上ある場合は改行
     *
     *@return string
     */
    public function showAllComments(): string
    {
        $dailyAdminComments = $this->getDailyAdminComments();
        $adminComments = [];
        foreach ($dailyAdminComments as $adminComment) {
            // dd($adminComment);
            $adminComments[] = $adminComment->admin == null ? "" : $adminComment->admin_description . " :" . $adminComment->admin_comment . " (" . $adminComment->admin->full_name . ")";
        }

        $allAdminCommentStr = implode("<br>", $adminComments);
        return $allAdminCommentStr;
    }
}
