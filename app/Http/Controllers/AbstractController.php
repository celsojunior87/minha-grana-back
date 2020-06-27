<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use mysql_xdevapi\Exception;

abstract class AbstractController extends Controller
{
    protected $with = [];
    protected $service;
    protected $requestValidate = '';

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->ok($this->service->getAll($request->all(), $this->with));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        try {
            if(!empty($this->requestValidate)) {
                $requestValidate = app($this->requestValidate);
                $request->validate($requestValidate->rules());
            }
        } catch (ValidationException $e) {
            return $this->error('Ops', $e->errors());
        }

        try {
            DB::beginTransaction();
            $response = $this->service->save($request->all());
            DB::commit();
            return $this->success('Operação realizada com com sucesso', ['response' => $response]);
        } catch (\Exception | ValidationException $e) {
            DB::rollBack();
            if($e instanceof ValidationException) {
                return $this->error('Ops', $e->errors());
            }
            if($e instanceof \Exception) {
                return $this->error($e->getMessage());
            }
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            if(!empty($this->requestValidate)) {
                $requestValidate = app($this->requestValidate);
                $request->validate($requestValidate->rules());
            }
        } catch (ValidationException $e) {
            return $this->error('Ops', $e->errors());
        }

        try {
            DB::beginTransaction();
            $this->service->update($id, $request->all());
            DB::commit();
            return $this->success('Operação realizada com com sucesso');
        } catch (\Exception | ValidationException $e) {
            DB::rollBack();
            if($e instanceof \Exception) {
                return $this->error($e->getMessage());
            }
            if($e instanceof ValidationException) {
                return $this->error('Ops', $e->errors());
            }
        }
    }

    /**
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        try {
            return $this->ok($this->service->find($id, $this->with));
        } catch (\Exception | ValidationException $e) {
            DB::rollBack();
            if ($e instanceof \Exception) {
                return $this->error($e->getMessage());
            }
            if ($e instanceof ValidationException) {
                return $this->error('Ops', $e->errors());
            }
        }
    }


    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $this->service->delete($id);
            return $this->success('Excluído com sucesso');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @param null $id
     * @return Response
     */
    public function preRequisite($id = null)
    {
        $preRequisite = $this->service->preRequisite($id);
        return $this->ok(compact('preRequisite'));
    }

    /**
     * @return Response
     */
    public function toSelect()
    {
        return $this->ok($this->service->toSelect());
    }
}
