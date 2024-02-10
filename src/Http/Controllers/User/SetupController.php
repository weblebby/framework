<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Weblebby\Framework\Services\User\SetupService;

class SetupController extends Controller
{
    public function index(Request $request, SetupService $setupService): View
    {
        $steps = $setupService->steps();
        $currentStep = $setupService->getCurrentStep($request->string('step'));
        $prevStep = $setupService->getPrevStep($currentStep);

        if ($currentStep === 'site') {
            $fields = $setupService->getSitePreferenceFields();
        } elseif ($currentStep === 'theme') {
            $fields = $setupService->getThemePreferenceFields();
        } else {
            $fields = [];
        }

        seo()->title(__('Build your site'));

        return view('weblebby::user.setup', compact(
            'steps',
            'currentStep',
            'prevStep',
            'fields',
        ));
    }

    public function update(Request $request, SetupService $setupService): RedirectResponse
    {
        $step = $setupService->getCurrentStep($request->string('step'));
        $nextStep = $setupService->getNextStep($step);

        if ($step === 'site') {
            $setupService->handleSiteStep($request);
        } elseif ($step === 'theme') {
            $setupService->handleThemeStep($request);
        } elseif ($step === 'variant') {
            $setupService->handleVariantStep($request);
        }

        if (is_null($nextStep)) {
            preference(['default::core->setup' => true]);

            return to_panel_route('dashboard');
        }

        return to_panel_route('setup.index', ['step' => $nextStep]);
    }
}
