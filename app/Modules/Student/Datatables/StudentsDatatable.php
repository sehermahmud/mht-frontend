<?php

namespace App\Modules\Student\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\Student\Models\Student;

class StudentsDatatable extends DataTable
{
    private $territory, $sector, $subscription_type;

    public function setTerritory($territory){
        $this->territory = $territory;
    }

    public function setSector($sector){
        $this->sector = $sector;
    }

    public function setSubscriptionType($subscription_type){
        $this->subscription_type = $subscription_type;
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
        ->eloquent($this->query())
        ->make(true);
    }

   /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $users = Student::select();

       return $this->applyScopes($users);
    }

   /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
        ->columns($this->getColumns())
        ->parameters([
            'dom' => 'Bfrtip',
            'buttons' => ['csv', 'excel', 'pdf', 'print', 'reset', 'reload']
        ]);
    }

   /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id',
            'name',
            'created_at',
            'updated_at'
        ];
    }

   /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'studentsdatatables_' . time();
    }
}