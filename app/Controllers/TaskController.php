<?php

namespace App\Controllers;

use App\Models\Task;
use App\Contracts\InputChain;
// Sentinel
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * CRUD - контроллер ресурса. Список задач.
 *
 * @author petrovich
 */
class TaskController
{

    /**
     * Инициализация Sentinel.
     *
     * @return void
     */
    private function sentinelBoot()
    {
        // Setup a new Eloquent Capsule instance
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => APP_DB_DRIVER,
            'host' => APP_DB_HOST,
            'database' => getenv('APP_DB_NAME'),
            'username' => getenv('APP_DB_USER'),
            'password' => getenv('APP_DB_PASSWORD'),
            'charset' => APP_DB_CHARSET,
            'collation' => APP_DB_CHARSET_COLLATION,
        ]);
        $capsule->bootEloquent();
    }

    /**
     * Регистрация нового пользователя.
     *
     * @return void
     */
    private function userRegister()
    {
        $this->sentinelBoot();
        // Register a new user
        Sentinel::registerAndActivate([
            'email' => 'admin',
            'password' => '123',
        ]);
    }

    /**
     * Вывод страницы аутентификации.
     *
     * @return mixed Ответ приложения
     */
    public function loginIndex()
    {
        // Инициализация Sentinel.
        $this->sentinelBoot();
        if (Sentinel::check()) {
            response()->redirect('/');
        }

        $data = view()->render('pages/login.php', [
            'error' => '',
        ]);
        return response()->setBody($data);
    }

    /**
     * Аутентификация.
     *
     * @return mixed Ответ приложения
     */
    public function login()
    {
        $request = request();
        $login = $request->input('login');
        $password = $request->input('password');

        // Инициализация Sentinel.
        $this->sentinelBoot();
        $credentials = [
            'email' => $login,
            'password' => $password,
        ];

        $error = '';
        try {
            if (Sentinel::authenticate($credentials)) {
                response()->redirect('/');
            } else {
                $error = 'Пользователь с указанным логином и паролем не найден.';
            }
        } catch (\Exception $e) {
            $error = 'Ошибка входа: ' . $e->getMessage();
        }

        $data = view()->render('pages/login.php', [
            'error' => $error,
        ]);
        return response()->setBody($data);
    }

    /**
     * Выход
     *
     * @return mixed Ответ приложения
     */
    public function logout()
    {
        // Инициализация Sentinel.
        $this->sentinelBoot();
        Sentinel::logout();
        response()->redirect('/');
    }

    /**
     * Получение пользователя
     *
     * @return mixed Объект пользователя или null
     */
    private function sentinelUser()
    {
        // Инициализация Sentinel.
        $this->sentinelBoot();
        return Sentinel::check();
    }

    /**
     * Вывод списка моделей.
     *
     * @return mixed Ответ приложения
     */
    public function index()
    {
        // Сортировка
        $request = request();
        $orderKey = 'order';
        $orderAttrs = ['user_name' => 'Имя пользователя', 'email' => 'E-mail', 'done' => 'Статус'];
        $prefixAsc = 'asc_by_';
        $prefixDesc = 'desc_by_';

        $orderBy = null;
        $orderDir = null;
        $orderValue = null;

        if ($request->hasInput($orderKey)) {
            $inputValue = $request->input($orderKey);
            foreach ($orderAttrs as $name => $desc) {
                if ($inputValue == ($prefixAsc . $name)) {
                    $orderBy = $name;
                    $orderDir = 'ASC';
                    $orderValue = $inputValue;
                } elseif ($inputValue == ($prefixDesc . $name)) {
                    $orderBy = $name;
                    $orderDir = 'DESC';
                    $orderValue = $inputValue;
                }
            }
        }

        $taskAttrValues = ['user_name' => '', 'email' => '', 'content' => ''];
        $errors = $taskAttrValues;
        $oldInputs = $taskAttrValues;

        $task = new Task('tasks');
        $pagination = pagination($task->count(), MODELS_PER_PAGE, currentPage());

        if ($orderBy) {
            $tasks = $task->select()->where()->orderBy($orderBy, $orderDir)
                            ->limit(MODELS_PER_PAGE)->offset($pagination['offset'])
                            ->execute()->fetchAll();
        } else {
            $tasks = $task->select()->where()
                            ->limit(MODELS_PER_PAGE)->offset($pagination['offset'])
                            ->execute()->fetchAll();
        }

        $data = view()->render('pages/tasks.php', [
            'user' => $this->sentinelUser(),
            'orderValue' => $orderValue,
            'orderKey' => $orderKey,
            'prefixAsc' => $prefixAsc,
            'prefixDesc' => $prefixDesc,
            'orderAttrs' => $orderAttrs,
            'added' => 0,
            'pagination' => $pagination,
            'oldInputs' => $oldInputs,
            'errors' => $errors,
            'tasks' => $tasks,
        ]);
        return response()->setBody($data);
    }

    /**
     * Создание новой модели.
     *
     * @return mixed Ответ приложения
     */
    public function store()
    {
        // Данные сортировки
        $orderKey = 'order';
        $orderAttrs = ['user_name' => 'Имя пользователя', 'email' => 'E-mail', 'done' => 'Статус'];
        $prefixAsc = 'asc_by_';
        $prefixDesc = 'desc_by_';

        $task = new Task('tasks');
        $added = 0;

        // Возможности шаблонизатора ограниченые,
        // поэтому для режима отображения ошибок (валидация не пройдена) делаем так
        $taskAttrValues = ['user_name' => '', 'email' => '', 'content' => ''];
        $errors = $taskAttrValues;
        $oldInputs = $taskAttrValues;

        // Обрабатываем входные значения
        $requestInputs = request()->inputs();
        $input = $task->input(['user_name', 'email', 'content'], $requestInputs);
        if ($input === true) {
            $task->save();
            $added = $task->id;
        } else {
            $errors = array_merge($errors, $input);
            // Получаем "старый ввод"
            // выделяем интересующие нас параметры из входных данных
            $arr = array_intersect_key($requestInputs, $taskAttrValues);
            // Дополняем массив, если в $requestInputs не все параметры
            $oldInputs = array_merge($taskAttrValues, $arr);
        }

        $pagination = pagination($task->count(), MODELS_PER_PAGE, currentPage());
        $tasks = $task->select()->where()->limit(MODELS_PER_PAGE)->offset($pagination['offset'])->execute()->fetchAll();

        $data = view()->render('pages/tasks.php', [
            'user' => $this->sentinelUser(),
            'orderValue' => '',
            'orderKey' => $orderKey,
            'prefixAsc' => $prefixAsc,
            'prefixDesc' => $prefixDesc,
            'orderAttrs' => $orderAttrs,
            'added' => $added,
            'pagination' => $pagination,
            'oldInputs' => $oldInputs,
            'errors' => $errors,
            'tasks' => $tasks,
        ]);
        return response()->setBody($data);
    }

    /**
     * Проверка на корректность ID модели.
     *
     * @param int $inputId Первичный ключ (ID) модели из входного запроса
     *
     * @return int ID модели.
     */
    private function inputChainID($inputId)
    {
        // Цепочка обработки входных данных (ID)
        $inputChain = inputChain()
                ->setChain([InputChain::FILTER_TRIM,
                    InputChain::RULE_NOT_EMPTY,
                    InputChain::RULE_INT])
                ->setData($inputId);
        // Проверка корректности ID
        if ($inputChain->handle() !== true) {
            response()->error(404);
        }
        return $inputChain->getData();
    }

    /**
     * Проверка на корректность ID модели и получение модели
     *
     * @param int $inputId Первичный ключ (ID) модели из входного запроса
     *
     * @return \App\Models\Task Модель.
     */
    private function findTaskOrFail($inputId)
    {
        $tasks = new Task('tasks');
        $task = $tasks->find($this->inputChainID($inputId));
        if (!isset($task)) {
            response()->error(404);
        }
        return $task;
    }

    /**
     * Вывод модели.
     *
     * @param int $inputId Первичный ключ (ID) модели
     *
     * @return mixed Ответ приложения
     */
    public function show($inputId)
    {
        $data = view()->render('pages/task.php', [
            'user' => $this->sentinelUser(),
            'task' => $this->findTaskOrFail($inputId),
        ]);
        return response()->setBody($data);
    }

    /**
     * Вывод страницы редактирования модели.
     *
     * @param int $inputId Первичный ключ (ID) модели
     *
     * @return mixed Ответ приложения
     */
    public function edit($inputId)
    {
        $user = $this->sentinelUser();
        if (!$user) {
            response()->redirect('/');
        }

        $task = $this->findTaskOrFail($inputId);
        // Возможности шаблонизатора ограниченые,
        // поэтому для режима отображения ошибок (валидация не пройдена) делаем так
        $errors = ['user_name' => '', 'email' => '', 'content' => '', 'done' => ''];
        $data = view()->render('pages/edit.php', [
            'user' => $user,
            'errors' => $errors,
            'task' => $task,
        ]);
        return response()->setBody($data);
    }

    /**
     * Сохранение модели.
     *
     * @param int $inputId Первичный ключ (ID) модели
     *
     * @return mixed Ответ приложения
     */
    public function update($inputId)
    {
        // Инициализация Sentinel.
        $user = $this->sentinelUser();
        if (!$user) {
            response()->redirect('/');
        }

        $task = $this->findTaskOrFail($inputId);
        $taskAttrValues = ['user_name' => '', 'email' => '', 'content' => ''];
        $oldAttributes = array_intersect_key($task->getAttributes(), $taskAttrValues);

        $errors = ['user_name' => '', 'email' => '', 'content' => '', 'done' => ''];
        // Обрабатываем входные значения
        $requestInputs = request()->inputs();
        $input = $task->input(['user_name', 'email', 'content', 'done'], $requestInputs);
        if ($input === true) {
            // Определяем, изменялись ли поля 'user_name', 'email', 'content'
            $newAttributes = array_intersect_key($task->getAttributes(), $taskAttrValues);
            if ($oldAttributes != $newAttributes) {
                $task->edited_by_admin = 1;
            }
            $task->save();
        } else {
            $errors = array_merge($errors, $input);
        }

        $data = view()->render('pages/edit.php', [
            'user' => $user,
            'errors' => $errors,
            'task' => $task,
        ]);
        return response()->setBody($data);
    }

    /**
     * Удаление модели.
     *
     * @param int $inputId Первичный ключ (ID) модели
     *
     * @return mixed Ответ приложения
     */
    public function destroy($inputId)
    {
        // Инициализация Sentinel.
        $user = $this->sentinelUser();
        if (!$user) {
            response()->redirect('/');
        }

        $task = $this->findTaskOrFail($inputId);
        $task->delete();
        response()->redirect('/');
    }

    /**
     * Вывод страницы контактов
     *
     * @return mixed Ответ приложения
     */
    public function contactsIndex()
    {
        $data = view()->render('pages/contacts.php', [
            'user' => $this->sentinelUser(),
        ]);
        return response()->setBody($data);
    }

    /**
     * Вывод содержимого markdown - файла
     *
     * @param string $markdownFile Путь к markdown - файлу
     *
     * @return mixed Ответ приложения
     */
    public function getMarkdownResponse($markdownFile)
    {
        // Парсим файл markdown
        $parsedown = new \Parsedown();
        if (!file_exists($markdownFile)) {
            response()->error(404);
        }
        $data = view()->render('pages/markdown.php', [
            'html' => $parsedown->text(file_get_contents($markdownFile)),
            'user' => $this->sentinelUser(),
        ]);
        return response()->setBody($data);
    }

    /**
     * Вывод страницы "О проекте"
     *
     * @return mixed Ответ приложения
     */
    public function aboutIndex()
    {
        return $this->getMarkdownResponse(APP_PATH_DIR_RESOURCES . '/content/about.md');
    }

    /**
     * Вывод страниц документации.
     *
     * @param int $name Имя документа
     *
     * @return mixed Ответ приложения
     */
    public function docsShow($name)
    {
        return $this->getMarkdownResponse(APP_PATH_DIR_RESOURCES . '/content/docs/' . $name . '.md');
    }

}
