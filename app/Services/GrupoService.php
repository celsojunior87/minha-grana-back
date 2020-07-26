<?php


namespace App\Services;


use App\Models\Status;
use App\Models\TipoGrupo;
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
        $arrItems = [];
        foreach ($grupos as $key => $grupo) {
            foreach ($grupo->items()->get() as $item) {
                $arrItems[] = $item->toArray();
            }
        }

        foreach ($arrItems as $key => $item) {
            $arrItems[$key]['status'] = $this->fazerCalculoStatus($item);
        }

        return $arrItems;
    }

    /**
     *
     *  Cálculo status
     *  STATUS: Seguir as seguintes regras:
     *  SE ESPERADO <> REALIZADO ENTÃO “Ajuste”
     *  SE ESPERADO = REALIZADO ENTÃO “Feito”
     *  SE ESPERADO <> 0 & REALIZADO = ENTÃO “Aguardando”
     *  SE ESPERADO = 0 & REALIZADO = 0 ENTÃO null
     *
     * @param $item
     * @return array|string[]
     */
    public function fazerCalculoStatus($item)
    {
        $valorSaldoEsperado = Arr::get($item, 'vl_saldo_esperado');
        $valorSaldoRealizado = Arr::get($item, 'vl_saldo_realizado');

        if ($valorSaldoEsperado !== 0 && $valorSaldoEsperado !== $valorSaldoRealizado) {
            return ['nome' => Status::find(Status::AGUARDANDO)->nome, 'color' => 'orange', 'text_color' => 'white'];
        }
        if ($valorSaldoEsperado === $valorSaldoRealizado) {
            return ['nome' => Status::find(Status::FEITO)->nome, 'color' => 'green', 'text_color' => 'white'];
        }
        if ($valorSaldoEsperado !== $valorSaldoRealizado) {
            return ['nome' => Status::find(Status::AJUSTE)->nome, 'color' => 'secondary', 'text_color' => 'white'];
        }
        return ['nome' => '', 'color' => 'default', 'text_color' => ''];
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

    /**
     * Cria um mês, quando o mesmo está em branco
     * @param $params
     * @return mixed|void
     * @throws \Exception
     */
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
                $this->criarGruposDefault($date);
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
                    $this->itemService->save($novoItem);
                }
            }
        }
    }

    /**
     * Cria apenas um grupo pro mês solicitado
     * @param $date
     * @return mixed
     */
    public function criarGruposDefault($date)
    {
        $this->criarReceitas($date);
        $this->criarDoacoes($date);
        $this->criarEconomias($date);
        $this->criarCasa($date);
        $this->criarDividas($date);
    }

    /**
     * Cria receitas
     * @param $date
     */
    public function criarReceitas($date)
    {
        $this->criarGrupoAbstract(
            'Receitas',
            auth()->user()->id,
            TipoGrupo::RECEITAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Criar doações
     * @param $date
     */
    public function criarDoacoes($date)
    {
        $this->criarGrupoAbstract(
            'Doacoes',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Criar economias
     * @param $date
     */
    public function criarEconomias($date)
    {
        $this->criarGrupoAbstract(
            'Economias',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Criar casas
     * @param $date
     */
    public function criarCasa($date)
    {
        $this->criarGrupoAbstract(
            'Casa',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * Dívidas
     * @param $date
     */
    public function criarDividas($date)
    {
        $this->criarGrupoAbstract(
            'Dívidas',
            auth()->user()->id,
            TipoGrupo::DESPESAS,
            Carbon::createFromFormat('Y-m', $date)->firstOfMonth()->format('Y-m-d')
        );
    }

    /**
     * @param $nomeGrupo
     * @param $userId
     * @param $tipoGrupoId
     * @param $date
     */
    public function criarGrupoAbstract($nomeGrupo, $userId, $tipoGrupoId, $date)
    {
        $grupo = [
            'nome' => $nomeGrupo,
            'user_id' => $userId,
            'tipo_grupo_id' => $tipoGrupoId,
            'date' => $date
        ];
        $this->save($grupo);
    }

    /**
     * Remove um grupo e todos seus itens relacionados
     * @param $date
     */
    public function delete($id)
    {
        $items = $this->find($id)->items()->get();
        foreach ($items as $item) {
            $this->itemService->getRepository()->delete($item->id);
        }
        return parent::delete($id);
    }
}
