<?php


namespace App\Repositories;


use App\Models\Grupo;

class GrupoRepository extends AbstractRepository
{
    protected $model;

    public function __construct(Grupo $model)
    {
        $this->model = $model;
    }

    /**
     * @param $params
     * @return array|mixed
     */
    public function formatParams($params)
    {
        $formatted = [];

        if (isset($params['nome'])) {
            $formatted['nome'] = $params['nome'];
        }

        if (isset($params['tipo_grupo'])) {
            $formatted['tipo_grupo_id'] = $params['tipo_grupo'];
        }

        if (isset($params['tipo_grupo_id'])) {
            $formatted['tipo_grupo_id'] = $params['tipo_grupo_id'];
        }

        if (isset($params['user_id'])) {
            $formatted['user_id'] = $params['user_id'];
        }

        if (isset($params['data'])) {
            $formatted['data'] = $params['data'];
        }

        return $formatted;
    }

    /**
     * @param $params
     * @return array|mixed
     */
    public function movimentacao($params, $with = [])
    {
        $arr = $this->model->with(['items'])->queryMovimentacao($params)->get();
    }
}
