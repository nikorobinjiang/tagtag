<?php
namespace App\Observers;

use App\Models\Material;

// 当模型已存在，不是新建的时候，依次触发的顺序是:
// saving -> updating -> updated -> saved

// 当模型不存在，需要新增的时候，依次触发的顺序则是:
// saving -> creating -> created -> saved

class BaseObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function creating($it)
    {
    }

    /**
     * 监听数据创建后的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function created($it)
    {
    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function updating($it)
    {
    }

    /**
     * 监听数据更新后的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function updated($it)
    {
    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function saving($it)
    {
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function saved($it)
    {
    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function deleting($it)
    {
    }

    /**
     * 监听数据删除后的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function deleted($it)
    {
    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function restoring($it)
    {
    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param  Model $it
     * @return void
     */
    public function restored($it)
    {
    }
}
