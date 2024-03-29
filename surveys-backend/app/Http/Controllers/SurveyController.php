<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Http\Resources\SurveyResource;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return SurveyResource::collection(Survey::where('user_id', $user->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSurveyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();
        #check and save image on local file system
        if(isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }

        $survey = Survey::create($data);

        #store survey question
        foreach($data['questions'] as $question) {
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question);
        }

        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey, Request $request)
    {
        $user = $request->user();
        if($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized action');
        }

        return new SurveyResource($survey);
    }


    public function showForGuest($slug)
    {
        $survey = Survey::where('slug', $slug)->first();
        return new SurveyResource($survey);
    }

    public function storeAnswer(StoreSurveyAnswerRequest $request, Survey $survey) {
        $validated = $request->validated();

        $surveyAnswer = SurveyAnswer::create([
            'survey_id'  => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date'   => date('Y-m-d H:i:s')
        ]);

        foreach($validated['answers'] as $questionId => $answer) {
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id])->get();
            if(!$question) {
                return response("Invalid question ID: \"$questionId\"", 400);
            }

            $data = [
                'survey_question_id' => $question[0]->id,
                'survey_answer_id'   => $surveyAnswer->id,
                'answer'             => is_array($answer) ? json_encode($answer) : $answer
            ];

            SurveyQuestionAnswer::create($data);
        }

        return response('', 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSurveyRequest  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();

        #check and save file on local file system
        if(isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;

            #if there is an old image delete it
            if($survey->image) {
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }

        $survey->update($data);

        #get ids of existing questions
        $existingIds = $survey->questions()->pluck('id')->toArray();
        #get ids of new questions
        $newIds = Arr::pluck($data['questions'], 'id');
        #find questions to delete
        $toDelete = array_diff($existingIds, $newIds);
        #find question to add
        $toAdd = array_diff($newIds, $existingIds);
        #delete questions $toDelete array
        SurveyQuestion::destroy($toDelete);
        #create new questions
        foreach($data['questions'] as $question) {
            if(in_array($question['id'], $toAdd)) {
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            }
        }

        #update existing questions
        $questionMap = collect($data['questions'])->keyBy('id');
        foreach($survey->questions as $question) {
            if(isset($questionMap[$question->id])) {
                $this->updateQuestion($question, $questionMap[$question->id]);
            }
        }

        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey, Request $request)
    {
        $user = $request->user();
        if($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized action');
        }

        #if there is an old image delete it
        if($survey->image) {
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        }

        $survey->delete();
        return response('', 204);
    }

    private function saveImage($image) {
        #check if image is valid base64 string
        if(preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            $image = substr($image, strpos($image, ',') + 1);

            #file extension
            $type = strtolower($type[1]);

            #check if file is an image
            if(!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }

            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if($image === false) {
                throw new \Exception('base64_decode failed');
            }

        }
        else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if(!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }

        file_put_contents($relativePath, $image);
        return $relativePath;
    }

    private function createQuestion($question) {
        if(is_array($question['data'])) {
            $question['data'] = json_encode($question['data']);
        }

        $validator = Validator::make($question, [
            'question'    => 'required|string',
            'type'        => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX
            ])],
            'description' => 'nullable|string',
            'data'        => 'present',
            'survey_id'   => 'exists:App\Models\Survey,id'
        ]);

        return SurveyQuestion::create($validator->validated());
    }

    private function updateQuestion(SurveyQuestion $question, $data) {
        if(is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }

        $validator = Validator::make($data, [
            'id'          => 'exists:App\Models\SurveyQuestion,id',
            'question'    => 'required|string',
            'type'        => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX
            ])],
            'description' => 'nullable|string',
            'data'        => 'present',
            'survey_id'   => 'exists:App\Models\Survey,id'
        ]);

        return $question->update($validator->validated());
    }
}
