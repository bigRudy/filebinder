<?php

App::import('Controller', 'Controller', false);
App::import('Component', 'Filebinder.Ring');

class FilebinderPost extends CakeTestModel{

    public $name = 'FilebinderPost';

    public $actsAs = array('Filebinder.Bindable');
}

/**
 *
 *
 *
 * @params
 */
class FilebinderPostsTestController extends Controller{

    public $name = 'FilebinderPostsTest';

    public $uses = array('FilebinderPost');

    public $components = array('Session', 'Filebinder.Ring');

    /**
     * redirect
     *
     * @param $url, $status = null, $exit = true
     * @return
     */
    public function redirect($url, $status = null, $exit = true){
        $this->redirectUrl = $url;
    }
}

class RingComponentTest extends CakeTestCase{

    public $fixtures = array('plugin.filebinder.attachment',
                             'plugin.filebinder.filebinder_post');

    function startTest() {
        $this->Controller = new FilebinderPostsTestController();
        $this->Controller->constructClasses();
        $this->Controller->params = array(
                                          'named' => array(),
                                          'pass' => array(),
                                          'url' => array());
    }

    function endTest() {
        unset($this->Controller);
        ClassRegistry::flush();
    }

    /**
     * testBindUp
     *
     * @return
     */
    function testBindUp(){
        $tmpPath = TMP . 'tests' . DS . 'bindup.png';

        // set test.png
        $this->_setTestFile($tmpPath);

        $this->Controller->FilebinderPost->bindFields = array(
                                                              array('field' => 'logo',
                                                                    'tmpPath'  => CACHE,
                                                                    'filePath' => TMP . 'tests' . DS,
                                                                    ),
                                                              );

        $this->Controller->data = array('FilebinderPost' => array());
        $this->Controller->data['FilebinderPost']['title'] = 'Title';
        $this->Controller->data['FilebinderPost']['logo'] = array('name' => 'logo.png',
                                                                  'tmp_name' => $tmpPath,
                                                                  'type' => 'image/png',
                                                                  'size' => 100,
                                                                  'error' => 0);

        $this->Controller->Component->init($this->Controller);
        $this->Controller->Component->initialize($this->Controller);
        $this->Controller->beforeFilter();

        $this->Controller->Ring->bindUp();

        $this->assertIdentical($this->Controller->data['FilebinderPost']['logo']['model'], 'FilebinderPost');
    }

    /**
     * testBindUp_move_uploaded_file
     *
     * @return
     */
    function testBindUp_move_uploaded_file(){
        $tmpPath = TMP . 'tests' . DS . 'bindup.png';

        // set test.png
        $this->_setTestFile($tmpPath);

        $this->Controller->FilebinderPost->bindFields = array(
                                                              array('field' => 'logo',
                                                                    'tmpPath'  => CACHE,
                                                                    'filePath' => TMP . 'tests' . DS,
                                                                    ),
                                                              );

        $this->Controller->data = array('FilebinderPost' => array());
        $this->Controller->data['FilebinderPost']['title'] = 'Title';
        $this->Controller->data['FilebinderPost']['logo'] = array('name' => 'logo.png',
                                                                  'tmp_name' => $tmpPath,
                                                                  'type' => 'image/png',
                                                                  'size' => 100,
                                                                  'error' => 0);

        $this->Controller->Component->init($this->Controller);
        $this->Controller->Component->initialize($this->Controller);
        $this->Controller->beforeFilter();

        $this->Controller->Ring->bindUp();

        // test.png is not uploaded file.
        $this->assertIdentical(file_exists($this->Controller->data['FilebinderPost']['logo']['tmp_bind_path']), false);
    }

    /**
     * test_bindDown
     *
     * @return
     */
    function test_bindDown(){
        $tmpPath = TMP . 'tests' . DS . 'binddown.png';

        // set test.png
        $this->_setTestFile($tmpPath);

        $this->Controller->FilebinderPost->bindFields = array(
                                                              array('field' => 'logo',
                                                                    'tmpPath'  => CACHE,
                                                                    'filePath' => TMP . 'tests' . DS,
                                                                    ),
                                                              );

        $this->Controller->data = array('FilebinderPost' => array());
        $this->Controller->data['FilebinderPost']['title'] = 'Title';
        $this->Controller->data['FilebinderPost']['logo'] = array('name' => 'logo.png',
                                                                  'tmp_name' => $tmpPath,
                                                                  'type' => 'image/png',
                                                                  'size' => 100,
                                                                  'error' => 0);

        $this->Controller->Component->init($this->Controller);
        $this->Controller->Component->initialize($this->Controller);
        $this->Controller->beforeFilter();

        $this->Controller->Ring->bindUp();
        $this->Controller->Ring->bindDown();

        $expected = $this->Controller->data['FilebinderPost']['logo'];
        $this->assertIdentical($this->Controller->Session->read('Filebinder.FilebinderPost.logo'), $expected);
    }

    /**
     * _setTestFile
     *
     * @return
     */
    function _setTestFile($to = null){
        if (!$to) {
            return false;
        }
        $from = APP . 'plugins/filebinder/tests/files/test.png';
        return copy($from, $to);
    }
}