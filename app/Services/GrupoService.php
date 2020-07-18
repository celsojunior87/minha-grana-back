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
        return parent::getAll($params, ['items']);
    }

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
}
