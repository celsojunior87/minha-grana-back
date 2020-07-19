<?php


namespace App\Services;


use App\Repositories\GrupoRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GrupoService extends AbstractService
{
    /**
     * @var GrupoRepository
     */
    protected $repository;

    /**
     * @var TipoGrupoService
     */
    protected $tipoGrupoService;

    public function __construct(GrupoRepository $repository, TipoGrupoService $tipoGrupoService)
    {
        $this->repository = $repository;
        $this->tipoGrupoService = $tipoGrupoService;
    }

    public function getAll($params = null, $with = null)
    {
        $grupos = parent::getAll($params, ['items']);
        foreach ($grupos as $key => $grupo) {
            $total_vl_esperado = 0;
            $total_vl_planejado = 0;
            $total_vl_recebido = 0;
            foreach($grupo->items()->get() as $item) {
                $total_vl_esperado += $item->vl_esperado;
                $total_vl_planejado += $item->vl_planejado;
                $total_vl_recebido += $item->vl_recebido;
            }
            $grupos[$key]['total_vl_esperado'] = $total_vl_esperado;
            $grupos[$key]['total_vl_planejado'] = $total_vl_planejado;
            $grupos[$key]['total_vl_recebido'] = $total_vl_recebido;
        }
        return $grupos;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function save(array $data)
    {
        $data['user_id'] = Auth::user()->id;
        $data['data'] = Carbon::now();
        return parent::save($data);
    }

    /**
     * @return mixed
     */
    public function preRequisite()
    {
        $arr['tipo_grupo'] = generateSelectOption($this->tipoGrupoService->getRepository()->list('id'));
        return $arr;
    }

    public function movimentacao($params)
    {
        return $this->repository->movimentacao($params);
    }
}
