<?php


namespace App\Services;


use App\Repositories\GrupoRepository;

class DashboardService extends AbstractService
{
    /**
     * @var GrupoRepository
     */
    protected $repository;

    /**
     * @var ItemService
     */
    public $itemService;

    public function __construct(GrupoRepository $repository,
                                ItemService $itemService
    )
    {
        $this->repository = $repository;
        $this->itemService = $itemService;
    }

    /**
     * names: ['MS', 'Apple', 'Google'],
     * values: [
     *    [10],
     *     [40],
     *      [90]
     *   ],
     */
    public function montarDashboardByGrupos($params = null, $with = [])
    {
        $grupos = $this->buscarInformacoesParaMontarGraficoPorGrupo($params, $with);
        $graph['colors'] = [];
        foreach ($grupos as $key => $grupo) {
            $graph['names'][] = $grupo['nome'];
            array_push($graph['colors'], 'red');
            $graph['values'][] = [$this->fazerCalculoPorcentagemPorGrupos($grupos, $grupo)];
        }
        return $graph;
    }

    public function fazerCalculoPorcentagemPorGrupos($grupos, $grupoAtual)
    {
        $totalValorEsperadoTodosGrupos = 0;
        foreach ($grupos as $grupo) {
            $totalValorEsperadoTodosGrupos += $grupo['total_vl_esperado'];
        }
        if ($totalValorEsperadoTodosGrupos == 0) {
            return 0;
        }
        return round($grupoAtual['total_vl_esperado'] / $totalValorEsperadoTodosGrupos * 100);
    }

    public function buscarInformacoesParaMontarGraficoPorGrupo($params, $with)
    {
        $with = ['items', 'tipoGrupo', 'items.itemMovimentacao'];
        $grupos = parent::getAll($params, $with)->toArray();
        foreach ($grupos as $key => $grupo) {
            $total_vl_esperado = 0;
            $total_vl_planeje = 0;
            $total_vl_recebido = 0;
            if (isset($grupo['items'])) {
                foreach ($grupo['items'] as $keyItems => $item) {
                    $grupos[$key]['items'][$keyItems]['vl_recebido'] = $this->somatoriaValorRealizadoItem($item);
                    $grupos[$key]['items'][$keyItems]['vl_planeje'] = $this->calculaPlaneje($item);
                    $grupos[$key]['items'][$keyItems]['class_vl_planeje'] = ($grupos[$key]['items'][$keyItems]['vl_planeje'] < 0) ? 'item_vl valor_negativo' : 'item_vl valor_positivo';
                    $grupos[$key]['items'][$keyItems]['vl_gasto'] = $this->somatoriaValorRealizadoItem($item);
                    $total_vl_esperado += $item['vl_esperado'];
                    $total_vl_planeje += $grupos[$key]['items'][$keyItems]['vl_planeje'];
                    $total_vl_recebido += $grupos[$key]['items'][$keyItems]['vl_recebido'];
                }
            }
            $grupos[$key]['total_vl_esperado'] = $total_vl_esperado;
            $grupos[$key]['total_vl_planeje'] = $total_vl_planeje;
            $grupos[$key]['total_vl_recebido'] = $total_vl_recebido;
        };
        return $grupos;
    }


    /**
     *  O valor esperado menos a soma do planejado
     */
    public function calculaPlaneje($item)
    {
        $vl_planeje = 0;
        $vl_planejado = 0;

        foreach ($item['item_movimentacao'] as $movimentacao) {
            $vl_planejado += $movimentacao['vl_planejado'];
        }

        $vl_planeje = $item['vl_esperado'] - $vl_planejado;
        if (empty($item['item_movimentacao'])) {

            $vl_planeje = 0;
        }
        return $vl_planeje;
    }

    public function somatoriaValorRealizadoItem($item)
    {
        $objItem = $this->itemService->getRepository()->find($item['id']);
        $movimentacoes = $objItem->itemMovimentacao()->get()->toArray();


        $vl_recebido = 0;
        foreach ($movimentacoes as $movimentacoe) {
            $vl_recebido += $movimentacoe['vl_realizado'];
        }
        return $vl_recebido;
    }


}
