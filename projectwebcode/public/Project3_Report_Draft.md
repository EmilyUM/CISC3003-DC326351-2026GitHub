# CISC3025 Natural Language Processing Project #3 Report

## Project Information

- **Project Title:** Person Name / Named Entity Recognition with a Maximum Entropy Markov Model
- **Course:** CISC3025 Natural Language Processing
- **Team Name:** [Please fill in your team name]
- **Team Members:** [Please fill in member names and student IDs]
- **Submission Components:** Source code, `model.pkl`, web application, Kaggle submission, report, presentation

## 1. Introduction

This project focuses on token-level named entity recognition (NER) on English news text derived from the CoNLL-2003 dataset. The task is to assign one BIO tag to each token in a sentence. The complete label set is `B-LOC`, `I-LOC`, `B-ORG`, `I-ORG`, `B-PER`, `I-PER`, `B-MISC`, `I-MISC`, and `O`.

The main goal of the project is not to redesign the whole training framework from scratch, but to improve the feature engineering part of the provided starter code. The course starter code already includes the basic machinery for training and prediction with a Maximum Entropy Markov Model (MEMM), while the feature set is intentionally weak. Therefore, the key challenge of this project is to design more informative lexical, contextual, and pattern-based features so that the model can better identify named entities and improve Macro F1 on the development set.

In addition to model development, the project also requires a runnable local version, a Kaggle-ready version for competition submission, and a web application to demonstrate the final NER model. In this work, I first organized the starter notebook into a local Python script and a Kaggle notebook, then improved the model step by step through controlled feature engineering experiments.

## 2. Description Of The Methods, Implementation, And Additional Considerations To Optimize The Model

### 2.1 Task Definition And Dataset

The dataset is provided in CSV format and follows a token-level NER setting.

- `train.csv`: labeled training set
- `dev.csv`: labeled validation set
- `test.csv`: unlabeled test set
- `sample_submission.csv`: Kaggle submission template

Each row corresponds to one token. Tokens are grouped into full sentences by `sentence_id`, and the order inside each sentence is defined by `token_idx`.

The dataset size used in this project is:

- `train.csv`: 204,567 tokens, 14,987 sentences
- `dev.csv`: 51,578 tokens, 3,466 sentences
- `test.csv`: 46,666 tokens, 3,684 sentences

### 2.2 Model Choice

The model used in this project is a Maximum Entropy Markov Model (MEMM). The implementation is based on the provided starter code and uses the NLTK `MaxentClassifier` as the underlying classifier. During training, each token is represented by a feature dictionary. During decoding, the prediction of the previous label is fed into the current token classification as part of the feature set.

This design is suitable for the project because:

- it matches the required starter-code framework;
- it allows flexible feature engineering;
- it is simple enough to debug and extend;
- it supports contextual modeling through the previous predicted label.

### 2.3 Implementation Work Completed

The original starter code was mainly provided as a notebook. To make the project easier to run, debug, and submit, I reorganized it into two runnable versions:

- A local script version: [local_memm_ner.py](file:///c:/Users/13599/Desktop/Project3/local_memm_ner.py)
- A Kaggle-ready notebook version: [kaggle_memm_ner.ipynb](file:///c:/Users/13599/Desktop/Project3/kaggle_memm_ner.ipynb)

The local script supports the following commands:

- `train-dev`: train on `train.csv` and evaluate on `dev.csv`
- `train-full`: train on `train.csv + dev.csv` and generate final model and submission
- `predict-test`: load a saved model and export a Kaggle submission file
- `demo`: run one custom input sentence through the trained model

This implementation work makes the system easier to reproduce and also prepares it for the later web application integration, because the script already includes sentence tokenization and `predict_sentence` logic.

### 2.4 Baseline Feature Design

Before optimization, the feature extractor already included a solid basic set of token-level and context-level features. These features are implemented in [features](file:///c:/Users/13599/Desktop/Project3/local_memm_ner.py#L99-L204).

The retained baseline-style features include:

- current word identity
- lowercased word form
- previous word and next word
- previous predicted label
- prefix and suffix features up to length 4
- word shape feature
- case-related features such as title case, uppercase, lowercase, and initial uppercase
- digit-related features
- punctuation, hyphen, apostrophe, and period indicators
- sentence boundary indicators `BOS` and `EOS`
- simple context combinations such as `prev_word + word` and `word + next_word`

These features already produced a strong starting point and were substantially better than the weak feature example shown in the project brief.

### 2.5 Optimization Process And Feature Engineering Experiments

The optimization strategy used in this project was simple and controlled: only one small group of features was added at a time, then the model was retrained using `train-dev`, and the resulting `Dev Macro F1` was compared with the previous best result.

This strategy is important because if too many features are changed at the same time, it becomes difficult to know which modification actually helped the model.

#### Experiment 1: Initial Strong Version

The first usable local version, based on the basic lexical and contextual features described above, achieved:

- Dev Macro F1 = **0.8590**

This served as the first strong reference point for later experiments.

#### Experiment 2: Long-Range Context Features

I first tried adding longer-range context features such as:

- `prev_prev_word`
- `next_next_word`
- related shape features
- simple long-range context combinations

The intuition was that wider context might help improve ORG and MISC entity boundaries. However, after running `train-dev`, the result dropped to:

- Dev Macro F1 = **0.8557**

This means the added long-range context features did not improve generalization on the development set. A likely reason is that these extra features made the model more specific to the training set without providing sufficiently robust evidence for unseen examples. Therefore, this whole feature group was removed.

#### Experiment 3: Organization Keyword Features

The next modification focused on organization-related lexical cues. I introduced a small keyword list named `ORG_KEYWORDS`, which includes words frequently found in organization names, such as:

- `company`
- `corp`
- `bank`
- `university`
- `committee`
- `group`
- `inc`
- `ltd`
- `association`
- `department`

The following features were added:

- whether the current word is an organization keyword
- whether the previous word is an organization keyword
- whether the next word is an organization keyword
- whether the current word ends with a common organization suffix such as `inc`, `corp`, `ltd`, or `co`

After adding this feature group, the development performance improved to:

- Dev Macro F1 = **0.8607**

This shows that adding targeted organization clues is useful, especially for organization-related labels.

#### Experiment 4: Consecutive Entity Pattern Features

After the ORG keyword feature group proved useful, I added another small group of features to model consecutive entity patterns. This was motivated by the observation that multi-token entities, especially organizations and miscellaneous entities, often contain several title-cased words or uppercase abbreviations in sequence.

The added features include:

- current and previous words are both title case
- current and next words are both title case
- current and previous words are both uppercase
- current and next words are both uppercase
- previous word is an ORG keyword and current word is title case
- previous word is an ORG keyword and current word is uppercase

After adding this group, the performance further improved to:

- Dev Macro F1 = **0.8625**

This became the best development result obtained in the project.

### 2.6 Summary Of The Final Feature Set

The final model retains the following useful feature groups:

- lexical identity features
- lowercase normalization
- prefix and suffix features
- word shape features
- case-pattern features
- punctuation and digit features
- previous label feature
- local left and right context features
- organization keyword features
- consecutive title-case and uppercase pattern features

The final optimized version is implemented in [local_memm_ner.py](file:///c:/Users/13599/Desktop/Project3/local_memm_ner.py).

### 2.7 Final Training And Submission Pipeline

After selecting the best-performing feature set according to `train-dev`, I retrained the final model on the full labeled data using:

```bash
python local_memm_ner.py train-full
```

This step trains on `train.csv + dev.csv` and outputs:

- `outputs/model.pkl`
- `outputs/submission.csv`

The final `submission.csv` file follows the required Kaggle format with two columns: `id,label`.

## 3. Evaluations And Discussions About The Findings

### 3.1 Best Development Result

The best development result achieved in this project is:

- Accuracy = **0.9709**
- Precision = **0.9185**
- Recall = **0.8185**
- Dev Macro F1 = **0.8625**

The detailed per-label result is:

| Label | Precision | Recall | F1-score |
| --- | ---: | ---: | ---: |
| B-LOC | 0.92 | 0.90 | 0.91 |
| I-LOC | 0.93 | 0.79 | 0.85 |
| B-ORG | 0.86 | 0.79 | 0.83 |
| I-ORG | 0.91 | 0.69 | 0.79 |
| B-PER | 0.92 | 0.85 | 0.88 |
| I-PER | 0.91 | 0.91 | 0.91 |
| B-MISC | 0.92 | 0.81 | 0.86 |
| I-MISC | 0.92 | 0.63 | 0.75 |
| O | 0.98 | 1.00 | 0.99 |

### 3.2 Comparative Experimental Results

The optimization process can be summarized as follows:

| Version | Main Change | Dev Macro F1 | Observation |
| --- | --- | ---: | --- |
| V1 | Strong basic lexical + contextual features | 0.8590 | Good starting point |
| V2 | Add long-range context (`prev_prev`, `next_next`) | 0.8557 | Worse, removed |
| V3 | Add ORG keyword features | 0.8607 | Improved, kept |
| V4 | Add consecutive entity pattern features | 0.8625 | Best result |

### 3.3 Discussion

Several important findings emerged from the experiments.

First, adding more features does not automatically improve the model. The failed long-range context experiment shows that even seemingly reasonable features can reduce development performance. This suggests that feature quality matters more than feature quantity.

Second, targeted features work better than generic expansion. The ORG keyword features improved the score because they captured meaningful lexical clues for organization names, which were particularly useful for difficult tags such as `B-ORG` and `I-ORG`.

Third, entity continuity matters. The consecutive title-case and uppercase features improved the model further because many named entities, especially organizations and multi-word names, appear as a sequence of similarly formatted tokens.

Fourth, the development results show that `PER` and `LOC` tags are relatively easier for the model, while `I-ORG` and `I-MISC` remain more challenging. This is reasonable because inner tokens of multi-word entities are harder to classify than the first token, and the boundary between `ORG` and `MISC` can be ambiguous in newswire text.

### 3.4 Relation To The Project Requirement

The project brief provides an example development Macro F1 around 0.8136 using a much weaker starter feature set. In this project, the optimized feature set achieved a development Macro F1 of 0.8625, which is a clear improvement over the reference baseline.

Therefore, the experiments demonstrate that the project goal was achieved: the provided MEMM framework was successfully improved through better feature engineering, resulting in a stronger and more practical NER system.

## 4. Conclusion And Future Work Suggestions

In this project, I built a complete MEMM-based named entity recognition pipeline based on the starter code and improved it through systematic feature engineering. I also reorganized the provided notebook into a local runnable script and a Kaggle-ready notebook, making the system easier to test, reproduce, and submit.

The final model achieved a best development Macro F1 of **0.8625**, improving over both the weaker starter-code example and the earlier local versions. The most useful improvements came from two targeted feature groups: organization keyword features and consecutive entity pattern features. In contrast, longer-range context features did not help and were removed after validation.

Overall, this project shows that careful feature design can significantly improve a classical MEMM model on a token-level NER task.

For future work, the following directions are promising:

- expand the keyword lists using external gazetteers for person names, organizations, and locations;
- design more robust abbreviation and acronym features for short uppercase entities;
- add better handling for multi-token entity boundaries;
- compare MEMM with stronger sequence models such as CRF or transformer-based models;
- build a complete web interface so that users can type a sentence and view token-level entity predictions visually.

## References

1. Erik F. Tjong Kim Sang and Fien De Meulder. *Introduction to the CoNLL-2003 Shared Task: Language-Independent Named Entity Recognition*. 2003.
2. NLTK documentation for maximum entropy classification.

## Appendix: Reproducibility Notes

Important project files:

- [local_memm_ner.py](file:///c:/Users/13599/Desktop/Project3/local_memm_ner.py)
- [kaggle_memm_ner.ipynb](file:///c:/Users/13599/Desktop/Project3/kaggle_memm_ner.ipynb)
- [competition_text.md](file:///c:/Users/13599/Desktop/Project3/Starter%20Code/Starter%20Code/docs/competition_text.md)

Useful commands:

```bash
python local_memm_ner.py train-dev
python local_memm_ner.py train-full
python local_memm_ner.py demo --model-path outputs/model.pkl --sentence "Barack Obama visited Paris."
```

Files generated for submission:

- `outputs/model.pkl`
- `outputs/submission.csv`
