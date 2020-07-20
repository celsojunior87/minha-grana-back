<?php


namespace App\Services;

use App\Repositories\GrupoRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

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

    /**
     * @var ItemService 
     */
    protected $itemService;

    public function __construct(GrupoRepository $repository, TipoGrupoService $tipoGrupoService, ItemService $itemService)
    {
        $this->repository = $repository;
        $this->tipoGrupoService = $tipoGrupoService;
        $this->itemService = $itemService;
    }

    public function getAll($params = null, $with = null)
    {
        $grupos = parent::getAll($params, ['items', 'tipoGrupo']);
        foreach ($grupos as $key => $grupo) {
            $total_vl_esperado = 0;
            $total_vl_planejado = 0;
            $total_vl_recebido = 0;
            foreach ($grupo->items()->get() as $item) {
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

    public function movimentacao($params)
    {
        $grupos = $this->repository->movimentacao($params);
        $arrGrupos = $grupos[0]['items'];
        dd($arrGrupos);
        foreach ($arrGrupos as $key => $value) {
            foreach ($value->items()->get() as $v) {

                dd($v);
            }
        }
        return $grupo;
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

//    public function movimentacao($params)
//    {
//        return $this->repository->movimentacao($params);
//    }

    public function criarMes($params)
    {
        if (empty($params['date'])) {
            throw new \Exception('A data é obrigatória');
        }

        $grupos = $this->getAll($params)->toArray();

        /**
         * Verifica se existe algo no mes atual
         */
        if (empty($grupos)) {

            /**
             * Faz a mesma consulta, baseada no mes anterior
             */
            $date = Arr::get($params, 'date');
            $mesAnterior = Carbon::createFromFormat('Y-m', $date)->subMonth(1)->format('Y-m');
            $gruposMesAnterior = $this->getAll(['date' => $mesAnterior])->toArray();

            /**
             * Se não existir nada no mês anterior, então cria um novo
             */
            if (empty($gruposMesAnterior)) {
                return $this->criarReceita($date);
            } else {
                /**
                 * Se existir, copia os itens do mês anterior.
                 */
                return $this->criarEspelhoMesAnterior($mesAnterior, $date);
            }
        }
    }

    /**
     * Cria exatamente o espelho do mês anterior para o mês atual
     * @param $mesAnterior
     * @param $mesAtual
     */
    public function criarEspelhoMesAnterior($mesAnterior, $mesAtual)
    {
        $gruposEspelho = $this->getAll(['date' => $mesAnterior]);
        foreach ($gruposEspelho as $grupo) {
            $novoGrupo = [
                'nome' => $grupo->nome,
                'user_id' => auth()->user()->id,
                'tipo_grupo_id' => $grupo->tipo_grupo_id,
                'data' => Carbon::createFromFormat('Y-m', $mesAtual)->firstOfMonth()->format('Y-m-d')
            ];
            $id = parent::save($novoGrupo)->id;
            if ($grupo->items()) {
                foreach ($grupo->items()->get() as $item) {
                    $novoItem = [
                        'nome' => $item->nome,
                        'vl_esperado' => $item->vl_esperado,
                        'vl_planejado' => $item->vl_planejado,
                        'vl_recebido' => $item->vl_recebido,
                        'grupo_id' => $id
                    ];
                    $this->itemSeronfvice->save($novoItem);
                }
            }
        }
    }

    /**
     * Cria apenas um grupo pro mês solicitado
     * @param $date
     * @return mixed
     */
    public function criarReceita($date)
    {
        $novoGrupo = [
            'nome' => 'Receitas',
            'user_id' => auth()->user()->id,
            'tipo_grupo_id' => TipoGrupo::RECEITAS,
            'date' => Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        ];
        return $this->save($novoGrupo);
    }
}
